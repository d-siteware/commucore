<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYear\Index;

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\FiscalYearTransaction;
use App\Pdfs\AnnualReportPdf;
use App\Services\Accounting\AnnualReportService;
use App\Services\Accounting\Datev\DatevExportService;
use App\Services\Accounting\FiscalYearService;
use App\Services\PdfGeneratorService;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            Flux::modal('fiscal-year-detail-modal')->show();
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

    public function exportSnapshot(int $year): StreamedResponse
    {
        $this->authorize('view-any', FiscalYear::class);

        $service = app(FiscalYearService::class);
        $snapshot = $service->getSnapshot($year);
        $filename = "fiscal_year_{$year}_snapshot.json";

        return response()->streamDownload(function () use ($snapshot): void {
            echo json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    /**
     * Lädt die gespeicherte DATEV-CSV-Datei herunter oder generiert sie neu.
     */
    public function downloadDatevCsv(int $year): StreamedResponse
    {
        $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();
        $path = \App\Enums\DatevExportType::BUCHUNGSSTAPEL->storagePath($year);

        if (! Storage::disk('local')->exists('private/'.$path)) {
            $path = app(DatevExportService::class)->export($fiscalYear);
        }

        return Storage::disk('local')->download('private/'.$path, "DATEV-Export-{$year}.csv");
    }

    // -----------------------------------------------------------------------
    // FiscalYear PDF (bestehend)
    // -----------------------------------------------------------------------

    public function downloadFiscalYearPdf(int $year): StreamedResponse
    {
        $this->authorize('view-any', FiscalYear::class);

        $snapshotData = app(FiscalYearService::class)->getSnapshot($year);

        $pdfContent = PdfGeneratorService::generatePdf(
            type: 'fiscal-year-report',
            data: [
                'year' => $year,
                'snapshot_data' => $snapshotData,
                'transactions' => $snapshotData['transactions'],
            ],
            filename: "Jahresabschluss-{$year}.pdf",
            restricted: true,
            locale: app()->getLocale(),
        );

        return response()->streamDownload(
            callback: static function () use ($pdfContent): void {
                echo $pdfContent;
            },
            name: "Jahresabschluss-{$year}.pdf",
            headers: ['Content-Type' => 'application/pdf'],
        );
    }

    // -----------------------------------------------------------------------
    // Annual Report PDF (neu)
    // -----------------------------------------------------------------------

    /**
     * Gibt true zurück wenn ein gespeicherter Jahresbericht existiert.
     */
    public function annualReportExists(int $year): bool
    {
        $fiscalYear = FiscalYear::where('year', $year)->first();

        if ($fiscalYear === null || $fiscalYear->annual_report_path === null) {
            return false;
        }

        return Storage::disk('local')->exists($fiscalYear->annual_report_path);
    }

    /**
     * Lädt den Jahresbericht aus dem Storage oder generiert ihn neu.
     * Speichert den Pfad an FiscalYear damit der Observer-Pfad genutzt wird.
     */
    public function downloadAnnualReport(int $year): StreamedResponse
    {
        $this->authorize('view-any', FiscalYear::class);

        $fiscalYear = FiscalYear::where('year', $year)->firstOrFail();
        $filename = "Jahresbericht-{$year}.pdf";

        // Aus Storage laden wenn vorhanden
        if (
            $fiscalYear->annual_report_path !== null
            && Storage::disk('local')->exists($fiscalYear->annual_report_path)
        ) {
            $pdfContent = Storage::disk('local')->get($fiscalYear->annual_report_path);

            return response()->streamDownload(
                callback: static function () use ($pdfContent): void {
                    echo $pdfContent;
                },
                name: $filename,
                headers: ['Content-Type' => 'application/pdf'],
            );
        }

        // Neu generieren + speichern
        $pdfContent = $this->generateAndStoreAnnualReport($fiscalYear);

        return response()->streamDownload(
            callback: static function () use ($pdfContent): void {
                echo $pdfContent;
            },
            name: $filename,
            headers: ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * Generiert den Jahresbericht, speichert ihn und gibt den PDF-Inhalt zurück.
     */
    private function generateAndStoreAnnualReport(FiscalYear $fiscalYear): string
    {
        $data = app(AnnualReportService::class)->build($fiscalYear->year);
        $filename = "Jahresbericht-{$fiscalYear->year}-".now()->format('Ymd').'.pdf';

        $pdf = new AnnualReportPdf(
            year: $data['year'],
            snapshot: $data['snapshot'],
            transactions: $data['transactions'],
            locale: app()->getLocale(),
        );
        $pdf->generateContent();
        $pdfContent = $pdf->Output($filename, 'S');

        $path = "reports/annual/{$fiscalYear->year}/{$filename}";
        Storage::disk('local')->put($path, $pdfContent);

        $fiscalYear->withoutEvents(function () use ($fiscalYear, $path): void {
            $fiscalYear->update(['annual_report_path' => $path]);
        });

        return $pdfContent;
    }

    // -----------------------------------------------------------------------

    private function getOpenYearData(int $year): array
    {
        $fiscalYear = FiscalYear::where('year', $year)->first()
            ?? FiscalYear::getOrCreate($year);

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
            'transactions' => $transactions->map(fn ($transaction): array => [
                'id' => $transaction->id,
                'date' => $transaction->date,
                'label' => $transaction->label,
                'amount' => $transaction->amount_gross,
                'type' => $transaction->type,
                'status' => $transaction->status,
                'locked_at' => null,
            ]),
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

        $selectedYear = FiscalYear::where('year', $year)->first();

        if ($selectedYear) {
            $hasTransactions = FiscalYearTransaction::where('fiscal_year_id', $selectedYear->id)->exists();

            if ($hasTransactions) {
                Flux::modal('delete-fiscal-year-modal')->show();

                return;
            }
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
