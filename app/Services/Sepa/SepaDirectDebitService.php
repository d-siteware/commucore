<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use DateTimeImmutable;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Illuminate\Support\Facades\Storage;

final class SepaDirectDebitService
{
    private const PAIN_FORMAT = 'pain.008.001.09';

    public function __construct(
        private readonly SepaMandateService $mandateService,
    ) {}

    /**
     * Generate a pain.008 XML for a single member fee collection.
     */
    public function generateSingle(
        Member $member,
        int $amountCents,
        string $remittanceInformation,
        Account $creditorAccount,
        string $creditorId,
    ): string {
        $mandate = $this->mandateService->getActiveMandate($member);

        if (! $mandate) {
            throw new \RuntimeException("Member {$member->id} has no active SEPA mandate.");
        }

        $messageId = 'SEPA-'.now()->format('YmdHis').'-'.$member->id;
        $facade = TransferFileFacadeFactory::createDirectDebit(
            $messageId,
            $creditorAccount->institute ?? $creditorAccount->name,
            self::PAIN_FORMAT,
        );

        $facade->addPaymentInfo('pmt-'.$member->id, [
            'id' => 'pmt-'.$member->id,
            'dueDate' => new DateTimeImmutable('+5 weekdays'),
            'creditorName' => $creditorAccount->name,
            'creditorAccountIBAN' => $creditorAccount->iban,
            'creditorAgentBIC' => $creditorAccount->bic,
            'seqType' => $mandate->last_used_at === null ? PaymentInformation::S_FIRST : PaymentInformation::S_RECURRING,
            'creditorId' => $creditorId,
            'localInstrumentCode' => $mandate->mandate_type->value === 'b2b' ? 'B2B' : 'CORE',
        ]);

        $facade->addTransfer('pmt-'.$member->id, [
            'amount' => $amountCents,
            'debtorIban' => $mandate->iban,
            'debtorBic' => $mandate->bic,
            'debtorName' => $mandate->account_holder,
            'debtorMandate' => $mandate->mandate_reference,
            'debtorMandateSignDate' => $mandate->mandate_date->format('d.m.Y'),
            'remittanceInformation' => $remittanceInformation,
            'endToEndId' => 'E2E-'.$member->id.'-'.now()->format('Ymd'),
        ]);

        $mandate->markAsUsed();

        return $facade->asXML();
    }

    /**
     * Generate pain.008 XML for batch collection of multiple members.
     */
    public function generateBatch(
        array $transactions,
        Account $creditorAccount,
        string $creditorId,
    ): string {
        $messageId = 'SEPA-BATCH-'.now()->format('YmdHis');
        $facade = TransferFileFacadeFactory::createDirectDebit(
            $messageId,
            $creditorAccount->institute ?? $creditorAccount->name,
            self::PAIN_FORMAT,
        );

        $frstGroup = [];
        $rcutGroup = [];

        foreach ($transactions as $tx) {
            $member = $tx['member'];
            $mandate = $tx['mandate'] ?? $this->mandateService->getActiveMandate($member);

            if (! $mandate) {
                throw new \RuntimeException("Member {$member->id} has no active SEPA mandate.");
            }

            $tx['mandate'] = $mandate;

            if ($mandate->last_used_at === null) {
                $frstGroup[] = $tx;
            } else {
                $rcutGroup[] = $tx;
            }
        }

        foreach (['FRST' => $frstGroup, 'RCUR' => $rcutGroup] as $seqType => $group) {
            if ($group === []) {
                continue;
            }

            $pmtId = 'pmt-'.strtolower($seqType).'-'.now()->format('Ymd');
            $facade->addPaymentInfo($pmtId, [
                'id' => $pmtId,
                'dueDate' => new DateTimeImmutable('+5 weekdays'),
                'creditorName' => $creditorAccount->name,
                'creditorAccountIBAN' => $creditorAccount->iban,
                'creditorAgentBIC' => $creditorAccount->bic,
                'seqType' => $seqType === 'FRST' ? PaymentInformation::S_FIRST : PaymentInformation::S_RECURRING,
                'creditorId' => $creditorId,
                'localInstrumentCode' => 'CORE',
            ]);

            foreach ($group as $tx) {
                $member = $tx['member'];
                $mandate = $tx['mandate'];

                $facade->addTransfer($pmtId, [
                    'amount' => $tx['amount'],
                    'debtorIban' => $mandate->iban,
                    'debtorBic' => $mandate->bic,
                    'debtorName' => $mandate->account_holder,
                    'debtorMandate' => $mandate->mandate_reference,
                    'debtorMandateSignDate' => $mandate->mandate_date->format('d.m.Y'),
                    'remittanceInformation' => $tx['remittanceInformation'],
                    'endToEndId' => $tx['endToEndId'] ?? ('E2E-'.$member->id.'-'.now()->format('Ymd')),
                ]);

                $mandate->markAsUsed();
            }
        }

        return $facade->asXML();
    }

    /**
     * Generate and store the XML file to disk.
     */
    public function generateAndStore(
        Member $member,
        int $amountCents,
        string $remittanceInformation,
        Account $creditorAccount,
        string $creditorId,
        string $disk = 'local',
    ): string {
        $xml = $this->generateSingle(
            $member,
            $amountCents,
            $remittanceInformation,
            $creditorAccount,
            $creditorId,
        );

        $path = 'sepa/'.now()->format('Y/m').'/SEPA-'.$member->id.'-'.now()->format('YmdHis').'.xml';
        Storage::disk($disk)->put($path, $xml);

        return $path;
    }
}
