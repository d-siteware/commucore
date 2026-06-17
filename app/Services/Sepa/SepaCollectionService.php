<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Enums\BookingAccountArea;
use App\Enums\MemberFeeType;
use App\Enums\SepaMandateStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SepaCollectionService
{
    public function __construct(
        private readonly SepaDirectDebitService $sepaDirectDebit,
        private readonly SepaSettingsService $sepaSettings,
        private readonly SepaMandateService $mandateService,
        private readonly EbicsService $ebicsService,
    ) {}

    public function createFeeTransactions(int $year, ?int $bookingAccountId = null): Collection
    {
        $creditorAccount = $this->sepaSettings->creditorAccount();

        if (!$creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        $members = Member::query()
            ->whereNotNull('entered_at')
            ->whereNull('left_at')
            ->where('fee_type', '!=', MemberFeeType::FREE)
            ->whereHas('sepaMandates', fn ($q) => $q
                ->where('status', SepaMandateStatus::Active)
                ->whereNull('payment_completed_at')
            )
            ->whereDoesntHave('memberTransactions', fn ($q) => $q
                ->where('is_membership_fee', true)
                ->where('fee_year', $year)
            )
            ->with('activeSepaMandate')
            ->get();

        if ($members->isEmpty()) {
            return collect();
        }

        $created = [];

        DB::transaction(function () use ($members, $year, $creditorAccount, $bookingAccountId, &$created) {
            foreach ($members as $member) {
                $mandate = $this->mandateService->getActiveMandate($member);
                $amount = $member->fee_type->fee() * 12;

                $transaction = Transaction::create([
                    'date' => now(),
                    'label' => 'Mitgliedsbeitrag '.$year.' - '.$member->fullName(),
                    'reference' => 'FEE-'.$member->id.'-'.$year,
                    'description' => null,
                    'amount_gross' => $amount,
                    'vat' => 0,
                    'amount_net' => $amount,
                    'account_id' => $creditorAccount->id,
                    'booking_account_id' => $bookingAccountId,
                    'type' => TransactionType::Deposit,
                    'status' => TransactionStatus::submitted,
                    'area' => BookingAccountArea::IDEAL,
                ]);

                $memberTx = MemberTransaction::create([
                    'member_id' => $member->id,
                    'transaction_id' => $transaction->id,
                    'sepa_mandate_id' => $mandate?->id,
                    'is_membership_fee' => true,
                    'fee_year' => $year,
                ]);

                $created[] = $memberTx;
            }
        });

        return collect($created);
    }

    public function generateXml(Collection $memberTransactions): string
    {
        $creditorAccount = $this->sepaSettings->creditorAccount();
        $creditorId = $this->sepaSettings->creditorId();

        if (!$creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        $transactions = $memberTransactions->map(function (MemberTransaction $mt) {
            $mandate = $this->mandateService->getActiveMandate($mt->member);

            if (!$mandate) {
                throw new \RuntimeException("Member {$mt->member->id} has no active SEPA mandate.");
            }

            return [
                'member' => $mt->member,
                'mandate' => $mandate,
                'amount' => $mt->transaction->amount_net,
                'remittanceInformation' => 'Mitgliedsbeitrag '.$mt->fee_year.' - '.$mt->member->fullName(),
                'endToEndId' => 'E2E-'.$mt->member->id.'-'.$mt->fee_year,
            ];
        })->all();

        return $this->sepaDirectDebit->generateBatch(
            transactions: $transactions,
            creditorAccount: $creditorAccount,
            creditorId: $creditorId,
        );
    }

    public function markAsBooked(Collection $memberTransactions): void
    {
        DB::transaction(function () use ($memberTransactions) {
            foreach ($memberTransactions as $mt) {
                if ($mt->transaction) {
                    $mt->transaction->updateQuietly([
                        'status' => TransactionStatus::booked,
                    ]);
                }
            }
        });
    }

    public function uploadToEbics(string $xmlContent): void
    {
        if (!$this->ebicsService->isReadyForUpload()) {
            throw new \RuntimeException('EBICS is not ready for upload. Please complete the EBICS setup first.');
        }

        $this->ebicsService->uploadXml($xmlContent);
    }

    public function collect(int $year, ?int $bookingAccountId = null): array
    {
        $memberTransactions = $this->createFeeTransactions($year, $bookingAccountId);

        if ($memberTransactions->isEmpty()) {
            return ['transactions' => $memberTransactions, 'xml' => null];
        }

        $xml = $this->generateXml($memberTransactions);

        return [
            'transactions' => $memberTransactions,
            'xml' => $xml,
        ];
    }

    public function collectWithEbicsUpload(int $year, ?int $bookingAccountId = null): array
    {
        $result = $this->collect($year, $bookingAccountId);

        if ($result['xml'] === null) {
            return $result;
        }

        $this->uploadToEbics($result['xml']);

        $this->markAsBooked($result['transactions']);

        return $result;
    }
}
