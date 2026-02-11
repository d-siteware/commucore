<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Accounting\AccountReport;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use App\Models\MeetingMinute;
use App\Models\Membership\Member;
use App\Pdfs\AccountReportPdf;
use App\Pdfs\EventInvitationLetter;
use App\Pdfs\EventProgramLetter;
use App\Pdfs\EventReportPdf;
use App\Pdfs\MeetingMinutesPdf;
use App\Pdfs\MemberApplicationPdf;
use App\Pdfs\TransactionInvoicePdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class PdfGeneratorService
{
    /**
     * Generate a PDF based on type and data.
     *
     * @throws Exception
     */
    public static function generatePdf(string $type, mixed $data, ?string $filename = null, bool $restricted = false, ?string $locale = null): string
    {
        if ($restricted && ! Auth::check()) {
            throw new Exception('Authentication required to generate this PDF.');
        }

        $locale = $locale ?? app()->getLocale();

        return match ($type) {
            'member-application' => self::generateMemberApplicationPdf($data, $filename, $locale),
            'event-report' => self::generateEventReportPdf($data, $filename, $locale),
            'account-report' => self::generateAccountReportPdf($data, $filename, $locale),
            'invoice' => self::generateInvoicePdf($data['transaction'], $data['member'], $filename, $locale),
            'meeting-minute' => self::generateMeetingMinutePdf($data, $filename, $locale),
            'event-invitation-letter' => self::generateEventInvitationLetter($data, $filename, $locale),
            'event-programm-letter' => self::generateEventProgrammLetter($filename, $data, $locale),
            'membership-fees' => self::generateMembershipFeesPdf($data['payments'], $data['summary'], $data['year'], $filename, $locale), // NEU

            default => throw new Exception("Unknown PDF type: $type"),
        };
    }


    private static function generateMembershipFeesPdf(\Illuminate\Support\Collection $payments, array $summary, int $year, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "Mitgliedsbeitraege-{$year}-" . now()->format('Ymd') . '.pdf';
        $pdf = new \App\Pdfs\MembershipFeesPdf($payments, $summary, $year, $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateEventProgrammLetter($filename, $data, $locale): string
    {

        $pdf = new EventProgramLetter($data, $filename, $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateEventInvitationLetter(Event $event, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "mitgliedsantrag-{$event->id}-".now()->format('Ymd').'.pdf';
        $pdf = new EventInvitationLetter($event, Member::whereNull('email')->get(), 'Mitgliedsantrag', $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateMemberApplicationPdf(Member $member, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "mitgliedsantrag-{$member->id}-".now()->format('Ymd').'.pdf';
        $pdf = new MemberApplicationPdf($member, 'Mitgliedsantrag', $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateEventReportPdf(Event $event, ?string $filename, string $locale): string
    {
        $ets = EventTransaction::query()->with('transaction')->where('event_id', $event->id)->get();
        $total_income = $ets->where('transaction.type', 'deposit')->sum('transaction.amount_gross') / 100;
        $total_spending = $ets->where('transaction.type', 'withdrawal')->sum('transaction.amount_gross') / 100;
        $incomes = $ets->where('transaction.type', 'deposit');
        $spending = $ets->where('transaction.type', 'withdrawal');
        $visitors = EventVisitor::all();

        $filename = $filename ?? "event-report-{$event->title[$locale]}-".now()->format('Ymd').'.pdf';
        $pdf = new EventReportPdf($event, $total_income, $incomes, $total_spending, $spending, $visitors, $locale, $filename);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateAccountReportPdf(AccountReport $accountReport, ?string $filename, string $locale): string
    {
        $dateString = $accountReport->period_start->format('Ymd');
        $filename = $filename ?? "Kassenbericht-{$dateString}.pdf";
        $pdf = new AccountReportPdf($accountReport, $locale, $filename);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateInvoicePdf(Transaction $transaction, ?Member $member, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "Rechnung-{$transaction->id}.pdf";
        $pdf = new TransactionInvoicePdf($transaction, $member, $locale);
        $pdf->generateContent();
        $pdfContent = $pdf->Output($filename, 'S');

        return $pdfContent;
    }

    private static function generateMeetingMinutePdf(MeetingMinute $meetingMinute, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "meeting-minute-{$meetingMinute->id}-".now()->format('Ymd').'.pdf';
        $pdf = new MeetingMinutesPdf($meetingMinute, $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    public static function generateMembershipApplication(Member $member): RedirectResponse
    {
        return redirect()->route('home');
    }
}
