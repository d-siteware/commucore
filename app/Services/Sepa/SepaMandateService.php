<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Enums\SepaMandateStatus;
use App\Enums\SepaMandateType;
use App\Models\Document;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Pdfs\SepaMandatePdf;
use App\Services\FeeService;
use Illuminate\Support\Facades\DB;

final class SepaMandateService
{
    public function __construct(
        private readonly FeeService $feeService,
    ) {}

    public function create(
        Member $member,
        string $iban,
        string $accountHolder,
        ?string $bic = null,
        SepaMandateType $type = SepaMandateType::Core,
        ?Document $signedDocument = null,
        ?string $notes = null,
    ): SepaMandate {
        return DB::transaction(function () use ($member, $iban, $accountHolder, $bic, $type, $signedDocument, $notes) {
            $mandate = SepaMandate::create([
                'member_id' => $member->id,
                'mandate_reference' => SepaMandate::generateReference($member),
                'iban' => $iban,
                'bic' => $bic,
                'account_holder' => $accountHolder,
                'mandate_date' => now(),
                'mandate_type' => $type,
                'status' => SepaMandateStatus::Active,
                'signed_document_id' => $signedDocument?->id,
                'notes' => $notes,
            ]);

            $member->update([
                'iban' => $iban,
                'bic' => $bic,
                'account_holder' => $accountHolder,
            ]);

            return $mandate;
        });
    }

    public function cancel(SepaMandate $mandate): void
    {
        $mandate->cancel();
    }

    public function getActiveMandate(Member $member): ?SepaMandate
    {
        /** @var SepaMandate|null $mandate */
        $mandate = $member->sepaMandates()
            ->where('status', SepaMandateStatus::Active)
            ->whereNull('payment_completed_at')
            ->latest()
            ->first();

        return $mandate;
    }

    public function hasActiveMandate(Member $member): bool
    {
        return $this->getActiveMandate($member) !== null;
    }

    public function generateMandatePdf(SepaMandate $mandate, string $creditorName, string $creditorId): string
    {
        $pdf = new SepaMandatePdf(
            member: $mandate->member,
            mandate: $mandate,
            creditorName: $creditorName,
            creditorId: $creditorId,
            periodAmount: $this->feeService->getAmountForMember($mandate->member),
            intervalLabel: $this->feeService->getInterval()->label(),
            periodsPerYear: $this->feeService->getPeriodsPerYear(),
        );

        return $pdf->generatePdf('SEPA-Mandat-'.$mandate->mandate_reference.'.pdf');
    }
}
