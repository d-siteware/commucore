<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\SepaCollection\Index;

use App\Enums\SepaMandateStatus;
use App\Enums\TransactionStatus;
use App\Models\Accounting\Transaction;
use App\Models\Membership\MemberTransaction;
use App\Services\Sepa\SepaCollectionService;
use App\Services\Sepa\SepaDirectDebitService;
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
    public function pendingCollections(): Collection
    {
        return MemberTransaction::query()
            ->where('is_membership_fee', true)
            ->where('fee_year', $this->selectedYear)
            ->whereHas('transaction', fn ($q) => $q->where('status', TransactionStatus::submitted))
            ->whereHas('member', fn ($q) => $q->whereHas('sepaMandates', fn ($sq) => $sq
                ->where('status', SepaMandateStatus::Active)
                ->whereNull('payment_completed_at')
            ))
            ->with(['member.activeSepaMandate', 'transaction'])
            ->get()
            ->map(function (MemberTransaction $mt) {
                $mandate = $mt->member->activeSepaMandate->first();

                return [
                    'member' => $mt->member,
                    'mandate' => $mandate,
                    'amount' => $mt->transaction->amount_net,
                    'fee_year' => $mt->fee_year,
                    'transaction' => $mt->transaction,
                ];
            });
    }

    #[Computed]
    public function returns(): array
    {
        return app(SepaReturnDebitService::class)->getRecentReturns();
    }

    #[Computed]
    public function availableYears(): array
    {
        return MemberTransaction::query()
            ->where('is_membership_fee', true)
            ->select('fee_year')
            ->distinct()
            ->whereNotNull('fee_year')
            ->orderBy('fee_year', 'desc')
            ->pluck('fee_year')
            ->toArray();
    }

    public function createTransactions(SepaCollectionService $collectionService): void
    {
        try {
            $memberTransactions = $collectionService->createFeeTransactions(
                year: $this->selectedYear,
            );

            $count = $memberTransactions->count();

            if ($count === 0) {
                Flux::toast(
                    text: __('sepa.collection.create_none'),
                    heading: __('sepa.collection.heading'),
                    variant: 'warning',
                );
            } else {
                Flux::toast(
                    text: __('sepa.collection.transactions_created', ['count' => $count]),
                    heading: __('sepa.collection.heading'),
                    variant: 'success',
                );
            }
        } catch (\RuntimeException $e) {
            Flux::toast(
                text: $e->getMessage(),
                variant: 'danger',
            );
        }
    }

    public function generateXml(
        SepaDirectDebitService $sepaService,
        SepaSettingsService $sepaSettings,
    ): mixed {
        $creditorAccount = $sepaSettings->creditorAccount();
        if (!$creditorAccount) {
            Flux::toast(text: __('sepa.direct_debit.errors.no_account'), variant: 'danger');

            return null;
        }

        $pending = $this->pendingCollections();

        if ($pending->isEmpty()) {
            Flux::toast(text: __('sepa.collection.pending_none'), variant: 'warning');

            return null;
        }

        $transactions = $pending->map(fn ($item) => [
            'member' => $item['member'],
            'mandate' => $item['mandate'],
            'amount' => $item['amount'],
            'remittanceInformation' => 'Mitgliedsbeitrag '.$item['fee_year'].' - '.$item['member']->fullName(),
            'endToEndId' => 'E2E-'.$item['member']->id.'-'.$item['fee_year'],
        ])->all();

        $xml = $sepaService->generateBatch(
            transactions: $transactions,
            creditorAccount: $creditorAccount,
            creditorId: $sepaSettings->creditorId(),
        );

        $filename = 'SEPA-Batch-'.$this->selectedYear.'-'.now()->format('YmdHis').'.xml';

        return response()->streamDownload(
            fn () => print ($xml),
            $filename,
            ['Content-Type' => 'application/xml']
        );
    }

    public function generateWithTransactions(
        SepaCollectionService $collectionService,
    ): mixed {
        try {
            $result = $collectionService->collect(year: $this->selectedYear);
        } catch (\RuntimeException $e) {
            Flux::toast(text: $e->getMessage(), variant: 'danger');

            return null;
        }

        if ($result['xml'] === null) {
            Flux::toast(text: __('sepa.collection.pending_none'), variant: 'warning');

            return null;
        }

        $filename = 'SEPA-Batch-'.$this->selectedYear.'-'.now()->format('YmdHis').'.xml';

        return response()->streamDownload(
            fn () => print ($result['xml']),
            $filename,
            ['Content-Type' => 'application/xml']
        );
    }

    public function uploadAndBook(
        SepaCollectionService $collectionService,
        SepaSettingsService $sepaSettings,
    ): void {
        if (!$sepaSettings->isEbicsConfigured()) {
            Flux::toast(
                text: __('sepa.collection.errors.ebics_not_configured'),
                heading: __('sepa.collection.heading'),
                variant: 'danger',
            );

            return;
        }

        try {
            $collectionService->collectWithEbicsUpload(year: $this->selectedYear);
        } catch (\RuntimeException $e) {
            Flux::toast(
                text: $e->getMessage(),
                variant: 'danger',
            );

            return;
        }

        Flux::toast(
            text: __('sepa.collection.messages.ebics_upload_success'),
            heading: __('sepa.collection.heading'),
            variant: 'success',
        );
    }

    public function recollect(
        int $transactionId,
        SepaReturnDebitService $returnService,
    ): void {
        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            Flux::toast(text: __('sepa.return_debit.errors.no_transaction'), variant: 'danger');

            return;
        }

        /** @var \App\Models\Membership\MemberTransaction|null $memberTx */
        $memberTx = $transaction->member_transaction()->where('is_membership_fee', true)->first();
        if (!$memberTx) {
            Flux::toast(text: __('sepa.return_debit.errors.no_transaction'), variant: 'danger');

            return;
        }

        $member = $memberTx->member;

        if (!$member) {
            Flux::toast(text: __('sepa.return_debit.errors.no_transaction'), variant: 'danger');

            return;
        }

        try {
            $returnService->recollect($transaction, $member);
        } catch (\RuntimeException $e) {
            Flux::toast(
                text: $e->getMessage(),
                variant: 'danger',
            );

            return;
        }

        Flux::toast(
            text: __('sepa.return_debit.messages.recollected'),
            heading: __('sepa.return_debit.messages.recollected'),
            variant: 'success',
        );
    }

    public function render(): View
    {
        return view('livewire.accounting.sepa-collection.index.page')
            ->title(__('sepa.collection.heading'));
    }
}
