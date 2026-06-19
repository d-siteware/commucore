<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Enums\BookingAccountArea;
use App\Enums\FeeInterval;
use App\Enums\MemberFeeType;
use App\Enums\SepaCollectionAttemptStatus;
use App\Enums\SepaMandateStatus;
use App\Enums\SepaSequenceType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Services\FeeService;
use Illuminate\Support\Carbon;
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
        private readonly FeeService $feeService,
    ) {}

    private function periodsElapsedInYear(Carbon $referenceDate): int
    {
        return match ($this->feeService->getInterval()) {
            FeeInterval::MONTHLY => $referenceDate->month,
            FeeInterval::QUARTERLY => $referenceDate->quarter,
            FeeInterval::BIANNUAL => $referenceDate->month <= 6 ? 1 : 2,
            FeeInterval::YEARLY => 1,
            FeeInterval::CUSTOM => throw new \RuntimeException(
                'SEPA-Sammeleinzug unterstützt das Beitragsintervall "custom" nicht.'
            ),
        };
    }

    public function findOpenCandidates(?Carbon $referenceDate = null): Collection
    {
        $referenceDate ??= now();
        $period = $this->periodForDate($referenceDate);
        $periodsElapsed = $this->periodsElapsedInYear($referenceDate);

        $creditorAccount = $this->sepaSettings->creditorAccount();

        if (! $creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        $eligible = Member::query()
            ->whereNotNull('entered_at')
            ->whereNull('left_at')
            ->where('fee_type', '!=', MemberFeeType::FREE)
            ->whereHas('sepaMandates', fn ($q) => $q
                ->where('status', SepaMandateStatus::Active)
                ->whereNull('payment_completed_at')
            )
            ->whereDoesntHave('sepaCollectionAttempts', fn ($q) => $q
                ->where('period_key', $period['key'])
                ->where('status', SepaCollectionAttemptStatus::Submitted)
            )
            ->with(['activeSepaMandate', 'memberTransactions' => fn ($q) => $q
                ->where('is_membership_fee', true)
                ->where('fee_year', $period['year'])
                ->with('transaction'),
            ])
            ->get();

        return $eligible->filter(function (Member $member) use ($periodsElapsed) {
            $expected = $this->feeService->getAmountForMember($member) * $periodsElapsed;

            $accumulated = $member->memberTransactions->sum(
                fn ($mt) => in_array($mt->transaction?->status, [TransactionStatus::booked, TransactionStatus::submitted], true)
                    ? ($mt->transaction->amount_net ?? 0)
                    : 0
            );

            return $accumulated < $expected;
        })->values();
    }

    public function createAttemptsAndGenerateXml(Collection $members, ?Carbon $referenceDate = null): array
    {
        if ($members->isEmpty()) {
            return ['xml' => null, 'attempts' => collect(), 'validation' => null];
        }

        $referenceDate ??= now();
        $period = $this->periodForDate($referenceDate);
        $this->assertYearIsOpen($period['year']);

        $creditorAccount = $this->sepaSettings->creditorAccount();
        $creditorId = $this->sepaSettings->creditorId();
        $painFormat = $this->sepaSettings->painFormat();
        $dueDate = Carbon::now()->addDays($this->sepaSettings->dueDateOffset());

        if (! $creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        $attempts = [];
        $debits = [];

        foreach ($members as $member) {
            $mandate = $this->mandateService->getActiveMandate($member);

            if (! $mandate) {
                throw new \RuntimeException("Member {$member->id} has no active SEPA mandate.");
            }

            $sequenceType = $mandate->last_used_at === null ? SepaSequenceType::Frst : SepaSequenceType::Rcur;
            $amount = $this->feeService->getAmountForMember($member);
            $endToEndId = 'E2E-'.$member->id.'-'.str_replace(['-', '/'], '', $period['key']);

            $debits[] = [
                'member' => $member,
                'mandate' => $mandate,
                'amount' => $amount,
                'remittanceInformation' => 'Mitgliedsbeitrag '.$period['key'].' - '.$member->fullName(),
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

        if (! $validation->valid) {
            return ['xml' => $xml, 'attempts' => collect(), 'validation' => $validation];
        }

        $batchReference = $this->extractMessageId($xml);

        DB::transaction(function () use ($debits, $period, $dueDate, $batchReference, &$attempts) {
            foreach ($debits as $debit) {
                $attempt = SepaCollectionAttempt::create([
                    'member_id' => $debit['member']->id,
                    'sepa_mandate_id' => $debit['mandate']->id,
                    'amount' => $debit['amount'],
                    'period_key' => $period['key'],
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

        if (! $creditorAccount) {
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
        if (! $this->ebicsService->isReadyForUpload()) {
            throw new \RuntimeException('EBICS is not ready for upload. Please complete the EBICS setup first.');
        }

        $this->ebicsService->uploadXml($xmlContent, $this->sepaSettings->painFormat());
    }

    private function extractMessageId(string $xml): string
    {
        preg_match('/<MsgId>([^<]+)<\/MsgId>/', $xml, $matches);

        return $matches[1] ?? 'batch-'.now()->format('YmdHis');
    }

    private function periodForDate(Carbon $referenceDate): array
    {
        return [
            'key' => $this->feeService->getInterval()->periodKey($referenceDate),
            'year' => $referenceDate->year,
        ];
    }

    private function assertYearIsOpen(int $year): void
    {
        $fiscalYear = FiscalYear::where('year', $year)->first();

        if (! $fiscalYear || $fiscalYear->isClosed()) {
            throw new \RuntimeException("Geschäftsjahr {$year} ist nicht offen. SEPA-Einzug für diesen Zeitraum ist nicht möglich.");
        }
    }
}
