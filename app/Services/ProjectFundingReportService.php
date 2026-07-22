<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FundingDocumentCategory;
use App\Enums\ProjectDocumentCategory;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Helpers\DateHelper;
use App\Models\Accounting\FiscalYear;
use App\Models\Document;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingPosition;
use App\Models\Funding\FundingTransaction;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use App\Pdfs\ProjectFundingReportPdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ProjectFundingReportService
{
    public function createProjectReport(Project $project, string $variant): Document
    {
        $data = $this->buildProjectData($project);
        $filename = $this->filename('Projektbericht', $project->title, $variant);

        $pdf = new ProjectFundingReportPdf('project', $variant, $data, app()->getLocale());
        $pdf->generateContent();

        return $this->storeDocument(
            model: $project,
            pdfContent: $pdf->Output($filename, 'S'),
            filename: $filename,
            category: ProjectDocumentCategory::Report->value,
            label: $variant === 'summary' ? 'Projektbericht Executive Summary' : 'Projektbericht Detailbericht',
        );
    }

    public function createFundingReport(Funding $funding, string $variant): Document
    {
        $data = $this->buildFundingData($funding);
        $filename = $this->filename('Foerderbericht', $funding->title, $variant);

        $pdf = new ProjectFundingReportPdf('funding', $variant, $data, app()->getLocale());
        $pdf->generateContent();

        return $this->storeDocument(
            model: $funding,
            pdfContent: $pdf->Output($filename, 'S'),
            filename: $filename,
            category: $variant === 'detailed'
                ? FundingDocumentCategory::UsageProof->value
                : FundingDocumentCategory::Report->value,
            label: match ($variant) {
                'summary' => 'Förderbericht Executive Summary',
                'statusbericht' => 'Status- und Mittelverwendungsbericht',
                default => 'Förderbericht Detailbericht',
            },
        );
    }

    private function buildProjectData(Project $project): array
    {
        $transactions = ProjectTransaction::query()
            ->with(['transaction.bookingAccount'])
            ->where('project_id', $project->id)
            ->whereHas('transaction', fn ($q) => $q
                ->where('status', TransactionStatus::booked->value)
                ->whereNot('type', TransactionType::Transfer->value)
            )
            ->get();

        $income = $transactions
            ->filter(fn (ProjectTransaction $pt) => $pt->transaction->type === TransactionType::Deposit)
            ->sum(fn (ProjectTransaction $pt): int => $pt->effectiveAmount());
        $expense = $transactions
            ->filter(fn (ProjectTransaction $pt) => $pt->transaction->type === TransactionType::Withdrawal)
            ->sum(fn (ProjectTransaction $pt): int => $pt->effectiveAmount());
        $fundingAllocated = $project->totalFundingAllocated();

        return [
            'title' => $project->title,
            'description' => $project->description ?? '',
            'status' => $project->status->label(),
            'period' => $this->period($project->start_date, $project->end_date),
            'reference' => '',
            'income' => (int) $income,
            'expense' => (int) $expense,
            'balance' => (int) $income - (int) $expense,
            'funding_allocated' => $fundingAllocated,
            'coverage_rate' => $expense > 0 ? round($fundingAllocated / $expense * 100, 1) : 0.0,
            'booking_account_type_name' => FiscalYear::contextFiscalYear()?->bookingAccountType->name ?? 'SKR42',
            'warnings' => $this->warnings($transactions),
            'transactions' => $this->transactionRows($transactions),
            'fundings' => $project->fundings()
                ->withPivot('allocated_amount')
                ->orderBy('title')
                ->get()
                ->map(fn (Funding $funding): array => [
                    'title' => $funding->title,
                    'funder' => $funding->funder ?? '',
                    'allocated_amount' => (int) ($funding->pivot->allocated_amount ?? 0),
                ])->toArray(),
        ];
    }

    private function buildFundingData(Funding $funding): array
    {
        $transactions = FundingTransaction::query()
            ->with(['transaction.bookingAccount'])
            ->where('funding_id', $funding->id)
            ->whereHas('transaction', fn ($q) => $q
                ->where('status', TransactionStatus::booked->value)
                ->whereNot('type', TransactionType::Transfer->value)
            )
            ->get();

        $received = $transactions
            ->filter(fn (FundingTransaction $ft) => $ft->transaction->type === TransactionType::Deposit)
            ->sum(fn (FundingTransaction $ft): int => $ft->effectiveAmount());
        $allocated = (int) $funding->projects()->sum('project_fundings.allocated_amount');
        $approved = (int) ($funding->approved_amount ?? 0);

        return [
            'title' => $funding->title,
            'description' => $funding->description ?? '',
            'status' => $funding->status->label(),
            'period' => $this->period($funding->funding_period_start, $funding->funding_period_end),
            'reference' => $funding->reference ?? '',
            'approved_amount' => $approved,
            'received' => (int) $received,
            'allocated_to_projects' => $allocated,
            'remaining' => $approved - $allocated,
            'booking_account_type_name' => FiscalYear::contextFiscalYear()?->bookingAccountType->name ?? 'SKR42',
            'warnings' => $this->warnings($transactions),
            'transactions' => $this->transactionRows($transactions),
            'position_groups' => $this->positionGroups($funding),
            'unassigned_actual' => $this->unassignedActual($funding),
            'projects' => $funding->projects()
                ->withPivot('allocated_amount')
                ->orderBy('title')
                ->get()
                ->map(fn (Project $project): array => [
                    'title' => $project->title,
                    'status' => $project->status->label(),
                    'allocated_amount' => (int) ($project->pivot->allocated_amount ?? 0),
                ])->toArray(),
        ];
    }

    /**
     * Plan/Ist je Förderposition, gruppiert nach Kategorie.
     * Plan = budget der Position (brutto), Ist = Summe der über
     * funding_transactions.funding_position_id verknüpften gebuchten Ausgaben.
     *
     * @return array<int, array{category: string, positions: array<int, array{title: string, budget: int, actual: int, remaining: int}>, budget_sum: int, actual_sum: int}>
     */
    private function positionGroups(Funding $funding): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, FundingPosition> $positions */
        $positions = $funding->fundingPositions()
            ->with('category')
            ->orderBy('title')
            ->get();

        return $positions
            ->map(fn (FundingPosition $position): array => [
                'category' => $position->category->name ?? '',
                'category_sort' => $position->category->sort ?? PHP_INT_MAX,
                'title' => $position->title,
                'budget' => (int) $position->budget,
                'actual' => $position->actualAmount(),
                'remaining' => $position->budget - $position->actualAmount(),
            ])
            ->sortBy([['category_sort', 'asc'], ['title', 'asc']])
            ->groupBy('category')
            ->map(fn (\Illuminate\Support\Collection $group, string $category): array => [
                'category' => $category,
                'positions' => $group
                    ->map(fn (array $row): array => [
                        'title' => $row['title'],
                        'budget' => $row['budget'],
                        'actual' => $row['actual'],
                        'remaining' => $row['remaining'],
                    ])
                    ->values()
                    ->toArray(),
                'budget_sum' => (int) $group->sum('budget'),
                'actual_sum' => (int) $group->sum('actual'),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Ist-Ausgaben ohne Positions-Zuordnung in Cent – damit der Statusbericht
     * auch dann vollständig ist, wenn Buchungen noch keiner Position zugeordnet sind.
     */
    private function unassignedActual(Funding $funding): int
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, FundingTransaction> $items */
        $items = FundingTransaction::query()
            ->with('transaction')
            ->where('funding_id', $funding->id)
            ->whereNull('funding_position_id')
            ->whereHas('transaction', fn ($q) => $q
                ->where('status', TransactionStatus::booked->value)
                ->where('type', TransactionType::Withdrawal->value)
            )
            ->get();

        return $items->sum(fn (FundingTransaction $ft): int => $ft->effectiveAmount());
    }

    private function storeDocument(Model $model, string $pdfContent, string $filename, string $category, string $label): Document
    {
        $uuid = Str::uuid()->toString();
        $type = Str::snake(class_basename($model::class));
        $dir = "documents/{$type}/{$model->getKey()}";
        $path = "{$dir}/{$uuid}";

        return DB::transaction(function () use ($model, $pdfContent, $filename, $category, $label, $uuid, $path): Document {
            Storage::disk('local')->put($path, $pdfContent);

            if (! ($model instanceof Project || $model instanceof Funding)) {
                throw new \InvalidArgumentException('Unsupported report document model.');
            }

            return $model->documents()->create([
                'uploaded_by_user_id' => Auth::id() ?? 0,
                'uuid' => $uuid,
                'original_name' => $filename,
                'disk' => 'local',
                'path' => $path,
                'mime_type' => 'application/pdf',
                'size' => strlen($pdfContent),
                'category' => $category,
                'label' => $label,
                'notes' => 'Automatisch generierter Bericht vom '.DateHelper::formatDateTime(now()),
            ]);
        });
    }

    private function transactionRows($transactions): array
    {
        return $transactions
            ->sortBy(fn ($item) => $item->transaction->date)
            ->map(fn ($item): array => [
                'id' => $item->transaction->id,
                'date' => DateHelper::formatDate($item->transaction->date),
                'type' => $item->transaction->type->label(),
                'label' => $item->transaction->label,
                'booking_account' => $item->transaction->bookingAccount?->number,
                'amount' => $item->effectiveAmount(),
            ])
            ->values()
            ->toArray();
    }

    private function warnings($transactions): array
    {
        return [
            'missing_booking_account' => $transactions
                ->filter(fn ($item) => $item->transaction->booking_account_id === null)
                ->count(),
        ];
    }

    private function period($start, $end): string
    {
        $startLabel = DateHelper::formatDate($start) ?: '-';
        $endLabel = DateHelper::formatDate($end) ?: '-';

        return $startLabel.' - '.$endLabel;
    }

    private function filename(string $prefix, string $title, string $variant): string
    {
        $slug = Str::slug($title);
        $variantName = match ($variant) {
            'summary' => 'summary',
            'statusbericht' => 'statusbericht',
            default => 'detail',
        };

        return $prefix.'-'.$variantName.'-'.$slug.'-'.now()->format('Ymd-His').'.pdf';
    }
}
