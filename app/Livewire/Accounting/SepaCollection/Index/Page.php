<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\SepaCollection\Index;

use App\Enums\SepaCollectionAttemptStatus;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Services\Sepa\SepaCollectionService;
use App\Services\Sepa\SepaReturnDebitService;
use App\Services\Sepa\SepaSettingsService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use WithPagination;

    public string $selectedTab = 'pending';

    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public function setSelectedTab(string $tab): void
    {
        $this->selectedTab = $tab;
    }

    #[Computed]
    public function openCandidates(): Collection
    {
        try {
            return app(SepaCollectionService::class)->findOpenCandidates($this->selectedYear);
        } catch (\RuntimeException) {
            return collect();
        }
    }

    #[Computed]
    public function unresolvedAttempts(): Collection
    {
        return SepaCollectionAttempt::query()
            ->with(['member', 'sepaMandate'])
            ->where('status', SepaCollectionAttemptStatus::Submitted)
            ->where('fee_year', $this->selectedYear)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn (SepaCollectionAttempt $a) => $a->batch_reference ?? 'ohne Batch');
    }

    #[Computed]
    public function returns(): Collection
    {
        return SepaCollectionAttempt::query()
            ->with(['member', 'sepaMandate', 'transaction'])
            ->where('status', SepaCollectionAttemptStatus::Returned)
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function history(): Collection
    {
        return SepaCollectionAttempt::query()
            ->with(['member', 'sepaMandate', 'transaction'])
            ->where('status', SepaCollectionAttemptStatus::Confirmed)
            ->where('fee_year', $this->selectedYear)
            ->orderBy('resolved_at', 'desc')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function availableYears(): array
    {
        return SepaCollectionAttempt::query()
            ->select('fee_year')
            ->distinct()
            ->whereNotNull('fee_year')
            ->orderBy('fee_year', 'desc')
            ->pluck('fee_year')
            ->toArray();
    }

    public function createAttempts(SepaCollectionService $collectionService): void
    {
        $candidates = $this->openCandidates();

        if ($candidates->isEmpty()) {
            Flux::toast(
                text: __('sepa.collection.create_none'),
                heading: __('sepa.collection.heading'),
                variant: 'warning',
            );

            return;
        }

        $result = $collectionService->createAttemptsAndGenerateXml(
            members: $candidates,
            year: $this->selectedYear,
        );

        $count = $result['attempts']->count();

        if ($count > 0) {
            Flux::toast(
                text: __('sepa.collection.transactions_created', ['count' => $count]),
                heading: __('sepa.collection.heading'),
                variant: 'success',
            );
        }

        if ($result['validation'] && !$result['validation']->valid) {
            Flux::toast(
                text: $result['validation']->toFlash(),
                heading: __('sepa.validation.step_validate'),
                variant: 'warning',
            );
        }
    }

    public function generateXml(SepaCollectionService $collectionService): mixed
    {
        $candidates = $this->openCandidates();

        if ($candidates->isEmpty()) {
            Flux::toast(
                text: __('sepa.collection.pending_none'),
                variant: 'warning',
            );

            return null;
        }

        $result = $collectionService->createAttemptsAndGenerateXml(
            members: $candidates,
            year: $this->selectedYear,
        );

        if ($result['xml'] === null) {
            Flux::toast(
                text: __('sepa.collection.pending_none'),
                variant: 'warning',
            );

            return null;
        }

        if ($result['validation'] && $result['validation']->valid) {
            Flux::toast(
                text: $result['validation']->toFlash(),
                variant: 'success',
            );
        } else {
            Flux::toast(
                text: $result['validation']?->toFlash() ?? 'Validierung fehlgeschlagen',
                heading: __('sepa.validation.step_validate'),
                variant: 'warning',
            );
        }

        $filename = 'SEPA-Batch-'.$this->selectedYear.'-'.now()->format('YmdHis').'.xml';

        return response()->streamDownload(
            fn () => print ($result['xml']),
            $filename,
            ['Content-Type' => 'application/xml'],
        );
    }

    public function uploadToEbics(
        SepaCollectionService $collectionService,
        SepaSettingsService $sepaSettings,
    ): void {
        if (!$sepaSettings->isEbicsConfigured()) {
            Flux::toast(
                text: __('sepa.collection.errors.ebics_not_configured'),
                variant: 'danger',
            );

            return;
        }

        $candidates = $this->openCandidates();

        if ($candidates->isEmpty()) {
            Flux::toast(
                text: __('sepa.collection.pending_none'),
                variant: 'warning',
            );

            return;
        }

        $result = $collectionService->createAttemptsAndGenerateXml(
            members: $candidates,
            year: $this->selectedYear,
        );

        if ($result['xml'] === null) {
            Flux::toast(
                text: __('sepa.collection.pending_none'),
                variant: 'warning',
            );

            return;
        }

        if ($result['validation'] && !$result['validation']->valid) {
            Flux::toast(
                text: $result['validation']->toFlash(),
                heading: __('sepa.validation.step_validate'),
                variant: 'danger',
            );

            return;
        }

        if ($result['validation'] && $result['validation']->valid) {
            Flux::toast(
                text: $result['validation']->toFlash(),
                variant: 'success',
            );
        }

        try {
            $collectionService->uploadToEbics($result['xml']);
        } catch (\RuntimeException $e) {
            Flux::toast(text: $e->getMessage(), variant: 'danger');

            return;
        }

        Flux::toast(
            text: __('sepa.collection.messages.ebics_upload_success'),
            heading: __('sepa.collection.heading'),
            variant: 'success',
        );
    }

    public function confirmAttempt(int $attemptId, SepaCollectionService $collectionService): void
    {
        $attempt = SepaCollectionAttempt::find($attemptId);

        if (!$attempt) {
            Flux::toast(text: __('sepa.return_debit.errors.no_transaction'), variant: 'danger');

            return;
        }

        try {
            $collectionService->confirm($attempt);

            Flux::toast(
                text: __('sepa.return_debit.messages.recollected'),
                heading: __('sepa.collection.heading'),
                variant: 'success',
            );
        } catch (\RuntimeException $e) {
            Flux::toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    public function confirmBatch(string $batchReference, SepaCollectionService $collectionService): void
    {
        try {
            $transactions = $collectionService->confirmBatch($batchReference);

            Flux::toast(
                text: $transactions->count().' Buchungen bestätigt',
                heading: __('sepa.collection.heading'),
                variant: 'success',
            );
        } catch (\RuntimeException $e) {
            Flux::toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    public function recollect(
        int $attemptId,
        SepaReturnDebitService $returnService,
    ): void {
        $attempt = SepaCollectionAttempt::find($attemptId);

        if (!$attempt) {
            Flux::toast(text: __('sepa.return_debit.errors.no_transaction'), variant: 'danger');

            return;
        }

        try {
            $result = $returnService->recollect($attempt);

            if ($result['validation'] && !$result['validation']->valid) {
                Flux::toast(
                    text: $result['validation']->toFlash(),
                    heading: __('sepa.validation.step_validate'),
                    variant: 'warning',
                );
            }

            Flux::toast(
                text: __('sepa.return_debit.messages.recollected'),
                variant: 'success',
            );
        } catch (\RuntimeException $e) {
            Flux::toast(
                text: $e->getMessage(),
                variant: 'danger',
            );
        }
    }

    public function render(): View
    {
        return view('livewire.accounting.sepa-collection.index.page')
            ->title(__('sepa.collection.heading'));
    }
}
