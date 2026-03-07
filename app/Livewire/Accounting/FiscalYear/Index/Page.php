<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYear\Index;

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\FiscalYearTransaction;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\Datev\DatevExportService;
use App\Services\Accounting\FiscalYearService;
use App\Services\PdfGeneratorService;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class Page extends Component
{
    public ?int $selectedYear = null;

    public bool $showDetailsModal = false;

    public ?array $snapshotData = null;

    #[On('fiscal-year-created')]
    public function refreshList(): void
    {
        unset($this->fiscalYears);

        Flux::modal('make-new-fiscal-year-modal')->close();
    }

    #[Computed]
    public function fiscalYears(): Collection
    {
        return FiscalYear::query()
            ->with(['openedBy', 'closedBy'])
            ->withCount('transactions')
            ->orderBy('year', 'desc')
            ->get();
    }

    public function showDetails(int $year): void
    {
        $this->selectedYear = $year;

        $service = app(FiscalYearService::class);
        $fiscalYear = FiscalYear::where('year', $year)->first();

        if ($fiscalYear && $fiscalYear->isClosed()) {
            $this->snapshotData = $service->getSnapshot($year);
        } else {
            $this->snapshotData = $this->getOpenYearData($year);
        }
        if ($this->snapshotData) {
            Flux::modal('fiscal-year-detail-modal')
                ->show();
        }
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedYear = null;
        $this->snapshotData = null;
    }

    public function navigateToClose(int $year): void
    {
        $this->redirect(route('fiscal-years.close', ['year' => $year]), navigate: true);
    }

    public function reopenFiscalYear(int $year): void
    {
        $this->authorize('reopen', FiscalYear::class);

        try {
            $service = app(FiscalYearService::class);
            $service->reopenFiscalYear($year);

            session()->flash('success', __('fiscal_year.reopened_successfully', ['year' => $year]));

            $this->closeDetailsModal();
            unset($this->fiscalYears);
        } catch (\Exception $e) {
            $this->addError('reopen', $e->getMessage());
        }
    }

    public function exportSnapshot(int $year)
    {
        $this->authorize('view-any', FiscalYear::class);

        try {
            $service = app(FiscalYearService::class);
            $snapshot = $service->getSnapshot($year);

            // Hier könntest du ein PDF generieren oder als JSON exportieren
            // Für jetzt: Download als JSON
            $filename = "fiscal_year_{$year}_snapshot.json";

            return response()->streamDownload(function () use ($snapshot): void {
                echo json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, $filename, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            $this->addError('export', $e->getMessage());
        }
    }

    /**
     * Lädt die gespeicherte DATEV-CSV-Datei herunter oder generiert sie neu.
     */
    public function downloadDatevCsv(int $year)
    {
        $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();

        $path = \App\Enums\DatevExportType::BUCHUNGSSTAPEL->storagePath($year);

        // Neu generieren, falls Datei nicht existiert
        if (! Storage::disk('local')->exists('private/'.$path)) {
            $path = app(DatevExportService::class)->export($fiscalYear);
        }

        $filename = "DATEV-Export-{$year}.csv";

        return Storage::disk('local')->download('private/'.$path, $filename);
    }

    // -----------------------------------------------------------------------
    // Jahresabschluss PDF Download
    // -----------------------------------------------------------------------

    /**
     * Generiert ein PDF-Dokument aus den Snapshot-Daten und bietet es zum Download an.
     */
    public function downloadFiscalYearPdf(int $year)
    {
        $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();

        // Snapshot-Daten laden (passe getSnapshotData() an deine Implementierung an)
        $snapshotData = $this->getSnapshotData($fiscalYear);

        $lockedTransactions = FiscalYearTransaction::where('fiscal_year_id', $fiscalYear->id)->get()->pluck('transaction_id');

        // Transaktionen laden
        $transactions = Transaction::query()
            ->with('account')
            ->whereIn('id',$lockedTransactions)
            ->get();

        $pdfContent = PdfGeneratorService::generatePdf(
            type: 'fiscal-year-report',
            data: [
                'year' => $year,
                'snapshot_data' => $snapshotData,
                'transactions' => $transactions,
            ],
            filename: "Jahresabschluss-{$year}.pdf",
            restricted: true,
            locale: app()->getLocale(),
        );

        $filename = "Jahresabschluss-{$year}.pdf";

        return response()->streamDownload(
            callback: static function () use ($pdfContent) {
                echo $pdfContent;
            },
            name: $filename,
            headers: ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * Hilfsmethode: Snapshot-Daten für ein FiscalYear aufbereiten.
     * Passe dies an deine bestehende getSnapshotData()-Logik an,
     * falls du die Daten schon im $snapshotData-Property hast.
     */
    private function getSnapshotData(FiscalYear $fiscalYear): array
    {
        // Wenn du bereits $this->snapshotData im Component hast und das Jahr stimmt,
        // kannst du direkt zurückgeben:
        if (isset($this->snapshotData) && isset($this->selectedYear) && $this->selectedYear === $fiscalYear->year) {
            return $this->snapshotData;
        }

        // Andernfalls aus dem gespeicherten JSON-Snapshot lesen:
        $snapshotPath = "private/fiscal-years/{$fiscalYear->year}/snapshot.json";

        if (Storage::disk('local')->exists($snapshotPath)) {
            $raw = json_decode(Storage::disk('local')->get($snapshotPath), true);

            return [
                'metadata' => [
                    'year' => $fiscalYear->year,
                    'opened_at' => isset($raw['metadata']['opened_at'])
                        ? \Carbon\Carbon::parse($raw['metadata']['opened_at'])
                        : null,
                    'opened_by' => $raw['metadata']['opened_by'] ?? null,
                    'closed_at' => isset($raw['metadata']['closed_at'])
                        ? \Carbon\Carbon::parse($raw['metadata']['closed_at'])
                        : null,
                    'closed_by' => $raw['metadata']['closed_by'] ?? null,
                    'is_closed' => $raw['metadata']['is_closed'] ?? false,
                ],
                'summary' => $raw['summary'] ?? [
                    'total_income' => 0,
                    'total_expense' => 0,
                    'balance' => 0,
                    'transaction_count' => 0,
                ],
            ];
        }

        // Fallback: Live aus der DB berechnen
        $transactions = Transaction::query()
            ->whereYear('booked_at', $fiscalYear->year)
            ->whereNotNull('booked_at')
            ->get();

        return [
            'metadata' => [
                'opened_at' => $fiscalYear->created_at,
                'opened_by' => null,
                'closed_at' => $fiscalYear->closed_at,
                'closed_by' => null,
                'is_closed' => (bool) $fiscalYear->closed_at,
            ],
            'summary' => [
                'total_income' => $transactions->where('type', 'deposit')->sum('amount_gross'),
                'total_expense' => $transactions->where('type', 'withdrawal')->sum('amount_gross'),
                'balance' => $transactions->where('type', 'deposit')->sum('amount_gross')
                    - $transactions->where('type', 'withdrawal')->sum('amount_gross'),
                'transaction_count' => $transactions->count(),
            ],
        ];
    }

    private function getOpenYearData(int $year): array
    {
        $fiscalYear = FiscalYear::where('year', $year)->first();

        if (! $fiscalYear) {
            $fiscalYear = FiscalYear::getOrCreate($year);
        }

        $transactions = \App\Models\Accounting\Transaction::whereYear('date', $year)
            ->with(['account', 'member_transaction', 'event_transaction'])
            ->get();

        return [
            'fiscal_year' => $fiscalYear,
            'metadata' => [
                'year' => $year,
                'opened_at' => $fiscalYear->opened_at,
                'closed_at' => null,
                'opened_by' => $fiscalYear->openedBy?->name,
                'closed_by' => null,
                'is_closed' => false,
            ],
            'transactions' => $transactions->map(function ($transaction): array {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->date,
                    'label' => $transaction->label,
                    'amount' => $transaction->amount_gross,
                    'type' => $transaction->type,
                    'status' => $transaction->status,
                    'locked_at' => null,
                ];
            }),
            'summary' => [
                'total_income' => $transactions->where('type', 'income')->sum('amount_gross'),
                'total_expense' => $transactions->where('type', 'expense')->sum('amount_gross'),
                'balance' => $transactions->where('type', 'income')->sum('amount_gross') -
                    $transactions->where('type', 'expense')->sum('amount_gross'),
                'transaction_count' => $transactions->count(),
            ],
        ];

    }

    public function openCreateFiscalYearModal(): void
    {
        Flux::modal('make-new-fiscal-year-modal')->show();
    }

    public function deleteFY(int $year): void
    {

        $this->authorize('delete', FiscalYear::class);

        $hasTransactions = false;

        $selectedYear = FiscalYear::where('year', $year)->first();

        if ($selectedYear) {

            $query = FiscalYearTransaction::where('fiscal_year_id', $selectedYear->id);
            $hasTransactions = $query->exists();

        }

        if ($hasTransactions) {

            Flux::modal('delete-fiscal-year-modal')->show();

            return;
        }

        FiscalYear::where('year', $year)->delete();

        Flux::toast(
            text: __('fiscal_year.deleted_successfully', ['year' => $year]),
            variant: 'success',
        );

        Flux::modal('fiscal-year-detail-modal')->close();

    }

    public function closeDeleteModal(): void
    {
        Flux::modal('delete-fiscal-year-modal')->close();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.fiscal-year.index.page')
            ->title(__('fiscal_year.index.title'));
    }
}
