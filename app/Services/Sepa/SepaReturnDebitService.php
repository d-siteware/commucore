<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Actions\Accounting\CancelTransaction;
use App\Enums\SepaCollectionAttemptStatus;
use App\Enums\SepaMandateType;
use App\Enums\SepaSequenceType;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Notifications\SepaReturnDebitNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SepaReturnDebitService
{
    public function __construct(
        private readonly SepaSettingsService $sepaSettings,
        private readonly SepaDirectDebitService $sepaDirectDebit,
        private readonly SepaXmlValidator $xmlValidator,
    ) {}

    public function handleReturn(
        SepaCollectionAttempt $attempt,
        string $returnReason,
        ?string $returnReference = null,
    ): void {
        DB::transaction(function () use ($attempt, $returnReason, $returnReference) {
            $returnInfo = 'Rücklastschrift: '.$returnReason;
            if ($returnReference) {
                $returnInfo .= ' (Ref: '.$returnReference.')';
            }

            match ($attempt->status) {
                SepaCollectionAttemptStatus::Submitted => $attempt->markReturned($returnInfo),

                SepaCollectionAttemptStatus::Confirmed => (function () use ($attempt, $returnInfo) {
                    $storno = CancelTransaction::handle($attempt->transaction, [
                        'user_id' => auth()->id(),
                        'reason' => $returnInfo,
                    ]);

                    $attempt->update([
                        'status' => SepaCollectionAttemptStatus::Returned,
                        'resolved_at' => now(),
                        'return_reason' => $returnInfo,
                        'reversal_transaction_id' => $storno->id,
                    ]);
                })(),

                default => throw new \RuntimeException(
                    'Dieser Versuch kann nicht als Rückläufer erfasst werden (Status: '.$attempt->status->value.').'
                ),
            };

            if ($attempt->sepaMandate) {
                $attempt->sepaMandate->update(['last_used_at' => null]);
            }

            $attempt->member->notify(new SepaReturnDebitNotification(
                member: $attempt->member,
                attempt: $attempt,
                reason: $returnReason,
            ));
        });
    }

    public function recollect(SepaCollectionAttempt $returnedAttempt): array
    {
        $mandate = $returnedAttempt->sepaMandate;

        if ($mandate && $mandate->mandate_type === SepaMandateType::B2b) {
            throw new \RuntimeException('Re-collection is not available for B2B mandates.');
        }

        if ($returnedAttempt->resolved_at && $returnedAttempt->resolved_at->lt(Carbon::now()->subDays(30))) {
            throw new \RuntimeException('Re-collection window has expired (more than 30 days since the return).');
        }

        $hasPending = SepaCollectionAttempt::query()
            ->where('member_id', $returnedAttempt->member_id)
            ->where('fee_year', $returnedAttempt->fee_year)
            ->where('status', SepaCollectionAttemptStatus::Submitted)
            ->exists();

        if ($hasPending) {
            throw new \RuntimeException('There is already a pending collection attempt for this member and year.');
        }

        $creditorAccount = $this->sepaSettings->creditorAccount();
        $creditorId = $this->sepaSettings->creditorId();
        $painFormat = $this->sepaSettings->painFormat();
        $dueDate = Carbon::now()->addDays($this->sepaSettings->dueDateOffset());

        if (!$creditorAccount) {
            throw new \RuntimeException('SEPA creditor account is not configured.');
        }

        $amount = $returnedAttempt->amount;
        $endToEndId = 'RECOLLECT-E2E-'.$returnedAttempt->id.'-'.now()->format('Ymd');

        $debits = [[
            'member' => $returnedAttempt->member,
            'mandate' => $mandate,
            'amount' => $amount,
            'remittanceInformation' => 'Wiedereinzug: '.$returnedAttempt->remittance_information,
            'endToEndId' => $endToEndId,
            'sequenceType' => SepaSequenceType::Frst,
        ]];

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

        $newAttempt = DB::transaction(function () use ($returnedAttempt, $mandate, $amount, $dueDate, $endToEndId, $batchReference) {
            $attempt = SepaCollectionAttempt::create([
                'member_id' => $returnedAttempt->member_id,
                'sepa_mandate_id' => $mandate?->id,
                'amount' => $amount,
                'fee_year' => $returnedAttempt->fee_year,
                'remittance_information' => 'Wiedereinzug: '.$returnedAttempt->remittance_information,
                'end_to_end_id' => $endToEndId,
                'due_date' => $dueDate,
                'sequence_type' => SepaSequenceType::Frst,
                'batch_reference' => $batchReference,
                'status' => SepaCollectionAttemptStatus::Submitted,
            ]);

            $mandate?->markAsUsed();

            return $attempt;
        });

        return ['xml' => $xml, 'attempts' => collect([$newAttempt]), 'validation' => $validation];
    }

    private function extractMessageId(string $xml): string
    {
        preg_match('/<MsgId>([^<]+)<\/MsgId>/', $xml, $matches);

        return $matches[1] ?? 'recollect-'.now()->format('YmdHis');
    }
}
