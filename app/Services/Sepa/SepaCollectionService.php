<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Enums\BookingAccountArea;
use App\Enums\MemberFeeType;
use App\Enums\SepaCollectionAttemptStatus;
use App\Enums\SepaMandateStatus;
use App\Enums\SepaSequenceType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Sepa\SepaCollectionAttempt;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SepaCollectionService
{
    public function __construct(
        private readonly SepaDirectDebitService $sepaDirectDebit,
        private readonly SepaSettingsService $sepaSettings,
        private readonly SepaMandateService $mandateService,
        private readonly EbicsService $ebicsService,
        private readonly SepaXmlValidator $xmlValidator,
    ) {}

    public function findOpenCandidates(int $year): Collection
    {
        $creditorAccount = $this->sepaSettings->creditorAccount();

        if (!$creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        return Member::query()
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
            ->whereDoesntHave('sepaCollectionAttempts', fn ($q) => $q
                ->where('fee_year', $year)
                ->where('status', SepaCollectionAttemptStatus::Submitted)
            )
            ->with('activeSepaMandate')
            ->get();
    }

    public function createAttemptsAndGenerateXml(Collection $members, int $year): array
    {
        if ($members->isEmpty()) {
            return ['xml' => null, 'attempts' => collect(), 'validation' => null];
        }

        $creditorAccount = $this->sepaSettings->creditorAccount();
        $creditorId = $this->sepaSettings->creditorId();
        $painFormat = $this->sepaSettings->painFormat();
        $dueDate = Carbon::now()->addDays($this->sepaSettings->dueDateOffset());

        if (!$creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        $attempts = [];
        $debits = [];

        foreach ($members as $member) {
            $mandate = $this->mandateService->getActiveMandate($member);

            if (!$mandate) {
                throw new \RuntimeException("Member {$member->id} has no active SEPA mandate.");
            }

            $sequenceType = $mandate->last_used_at === null ? SepaSequenceType::Frst : SepaSequenceType::Rcur;
            $amount = $member->fee_type->fee() * 12;
            $endToEndId = 'E2E-'.$member->id.'-'.$year;

            $debits[] = [
                'member' => $member,
                'mandate' => $mandate,
                'amount' => $amount,
                'remittanceInformation' => 'Mitgliedsbeitrag '.$year.' - '.$member->fullName(),
                'endToEndId' => $endToEndId,
                'sequenceType' => $sequenceType,
            ];
        }

        $xml = $this->sepaDirectDebit->generateBatch(
            debits: $debits,
            creditorAccount: $creditorAccount,
            creditorId: $creditorId,
            dueDate: $dueDate,
            painFormat: $painFormat,
        );

        $validation = $this->xmlValidator->validate($xml, $painFormat);

        if (!$validation->valid) {
            return ['xml' => $xml, 'attempts' => collect(), 'validation' => $validation];
        }

        $batchReference = $this->extractMessageId($xml);

        DB::transaction(function () use ($debits, $year, $dueDate, $batchReference, &$attempts) {
            foreach ($debits as $debit) {
                $attempt = SepaCollectionAttempt::create([
                    'member_id' => $debit['member']->id,
                    'sepa_mandate_id' => $debit['mandate']->id,
                    'amount' => $debit['amount'],
                    'fee_year' => $year,
                    'remittance_information' => $debit['remittanceInformation'],
                    'end_to_end_id' => $debit['endToEndId'],
                    'due_date' => $dueDate,
                    'sequence_type' => $debit['sequenceType'],
                    'batch_reference' => $batchReference,
                    'status' => SepaCollectionAttemptStatus::Submitted,
                ]);

                $debit['mandate']->markAsUsed();

                $attempts[] = $attempt;
            }
        });

        return ['xml' => $xml, 'attempts' => collect($attempts), 'validation' => $validation];
    }

    public function confirm(SepaCollectionAttempt $attempt): Transaction
    {
        if ($attempt->status !== SepaCollectionAttemptStatus::Submitted) {
            throw new \RuntimeException('Only submitted attempts can be confirmed.');
        }

        $creditorAccount = $this->sepaSettings->creditorAccount();

        if (!$creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        return DB::transaction(function () use ($attempt, $creditorAccount) {
            $transaction = Transaction::create([
                'date' => now(),
                'label' => 'SEPA-Lastschrift: '.$attempt->remittance_information,
                'reference' => 'SEPA-'.$attempt->id,
                'description' => 'SEPA-Einzug (Attempt #'.$attempt->id.', Batch: '.$attempt->batch_reference.')',
                'amount_gross' => $attempt->amount,
                'vat' => 0,
                'amount_net' => $attempt->amount,
                'account_id' => $creditorAccount->id,
                'type' => TransactionType::Deposit,
                'status' => TransactionStatus::booked,
                'area' => BookingAccountArea::IDEAL,
            ]);

            MemberTransaction::create([
                'member_id' => $attempt->member_id,
                'transaction_id' => $transaction->id,
                'sepa_mandate_id' => $attempt->sepa_mandate_id,
                'is_membership_fee' => true,
                'fee_year' => $attempt->fee_year,
            ]);

            $attempt->confirm($transaction);

            return $transaction;
        });
    }

    public function confirmBatch(string $batchReference): Collection
    {
        $attempts = SepaCollectionAttempt::query()
            ->unresolved()
            ->inBatch($batchReference)
            ->get();

        $transactions = collect();

        foreach ($attempts as $attempt) {
            $transactions->push($this->confirm($attempt));
        }

        return $transactions;
    }

    public function uploadToEbics(string $xmlContent): void
    {
        if (!$this->ebicsService->isReadyForUpload()) {
            throw new \RuntimeException('EBICS is not ready for upload. Please complete the EBICS setup first.');
        }

        $this->ebicsService->uploadXml($xmlContent);
    }

    private function extractMessageId(string $xml): string
    {
        preg_match('/<MsgId>([^<]+)<\/MsgId>/', $xml, $matches);

        return $matches[1] ?? 'batch-'.now()->format('YmdHis');
    }
}
