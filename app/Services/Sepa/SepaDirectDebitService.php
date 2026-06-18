<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Enums\SepaSequenceType;
use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use Carbon\Carbon;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;

final class SepaDirectDebitService
{
    /**
     * Generate pain.008 XML for a batch of direct debits.
     *
     * @param array<int, array{
     *     member: Member,
     *     mandate: SepaMandate,
     *     amount: int,
     *     remittanceInformation: string,
     *     endToEndId: string,
     *     sequenceType: SepaSequenceType,
     * }> $debits
     */
    public function generateBatch(
        array $debits,
        Account $creditorAccount,
        string $creditorId,
        Carbon $dueDate,
        string $painFormat,
    ): string {
        $messageId = 'SEPA-BATCH-'.now()->format('YmdHis');
        $facade = TransferFileFacadeFactory::createDirectDebit(
            $messageId,
            $creditorAccount->name,
            $painFormat,
        );

        $grouped = [];
        foreach ($debits as $debit) {
            $seqType = $debit['sequenceType']->value;
            $grouped[$seqType][] = $debit;
        }

        foreach ($grouped as $seqType => $group) {
            $pmtId = 'pmt-'.strtolower($seqType).'-'.now()->format('Ymd');
            $facade->addPaymentInfo($pmtId, [
                'id' => $pmtId,
                'dueDate' => $dueDate,
                'creditorName' => $creditorAccount->name,
                'creditorAccountIBAN' => $creditorAccount->iban,
                'creditorAgentBIC' => $creditorAccount->bic,
                'seqType' => $seqType === 'FRST' ? PaymentInformation::S_FIRST : PaymentInformation::S_RECURRING,
                'creditorId' => $creditorId,
                'localInstrumentCode' => $group[0]['mandate']->mandate_type->isB2b() ? 'B2B' : 'CORE',
            ]);

            foreach ($group as $debit) {
                $facade->addTransfer($pmtId, [
                    'amount' => $debit['amount'],
                    'debtorIban' => $debit['mandate']->iban,
                    'debtorBic' => $debit['mandate']->bic,
                    'debtorName' => $debit['mandate']->account_holder,
                    'debtorMandate' => $debit['mandate']->mandate_reference,
                    'debtorMandateSignDate' => $debit['mandate']->mandate_date->format('d.m.Y'),
                    'remittanceInformation' => $debit['remittanceInformation'],
                    'endToEndId' => $debit['endToEndId'],
                ]);
            }
        }

        return $facade->asXML();
    }
}
