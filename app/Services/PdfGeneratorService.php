<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Accounting\AccountReport;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use App\Models\Membership\Member;
use App\Models\Protocols\Minutes\MeetingMinute;
use App\Pdfs\AccountReportPdf;
use App\Pdfs\AnnualReportPdf;
use App\Pdfs\EventInvitationLetter;
use App\Pdfs\EventProgramLetter;
use App\Pdfs\EventReportPdf;
use App\Pdfs\FiscalYearReportPdf;
use App\Pdfs\MeetingMinutesPdf;
use App\Pdfs\MemberApplicationPdf;
use App\Pdfs\MembershipFeesPdf;
use App\Pdfs\TransactionInvoicePdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PdfGeneratorService
{
    /**
     * Generate a PDF based on type and data.
     *
     * Instance method — delegates to the static version so callers can mock via DI.
     *
     * @throws Exception
     */
    public function generate(string $type, mixed $data, ?string $filename = null, bool $restricted = false, ?string $locale = null): string
    {
        return self::generatePdf($type, $data, $filename, $restricted, $locale);
    }

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
            'membership-fees' => self::generateMembershipFeesPdf($data['payments'], $data['summary'], $data['year'], $filename, $locale),
            'fiscal-year-report' => self::generateFiscalYearReportPdf($data['year'], $data['snapshot_data'], $data['transactions'], $filename, $locale),
            'annual-report' => self::generateAnnualReportPdf($data['year'], $data['snapshot'], $data['transactions'], $filename, $locale),
            default => throw new Exception("Unknown PDF type: $type"),
        };
    }

    private static function generateMembershipFeesPdf(Collection $payments, array $summary, int $year, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "Mitgliedsbeitraege-{$year}-".now()->format('Ymd').'.pdf';
        $pdf = new MembershipFeesPdf($payments, $summary, $year, $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateEventProgrammLetter(?string $filename, $data, $locale): string
    {
        $pdf = new EventProgramLetter($data, $filename, $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateEventInvitationLetter(Event $event, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "mitgliedsantrag-{$event->id}-".now()->format('Ymd').'.pdf';
        $pdf = new EventInvitationLetter($event, Member::whereNull('email')
            ->get(), 'Mitgliedsantrag', $locale);
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
        $ets = EventTransaction::query()
            ->with('transaction')
            ->where('event_id', $event->id)
            ->get();
        $total_income = $ets->where('transaction.type', 'deposit')
            ->sum('transaction.amount_gross') / 100;
        $total_spending = $ets->where('transaction.type', 'withdrawal')
            ->sum('transaction.amount_gross') / 100;
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

        return $pdf->Output($filename, 'S');
    }

    private static function generateMeetingMinutePdf(MeetingMinute $meetingMinute, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "meeting-minute-{$meetingMinute->id}-".now()->format('Ymd').'.pdf';
        $pdf = new MeetingMinutesPdf($meetingMinute, $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateFiscalYearReportPdf(
        int $year,
        array $snapshotData,
        Collection $transactions,
        ?string $filename,
        string $locale,
    ): string {
        $filename = $filename ?? "Jahresabschluss-{$year}-".now()->format('Ymd').'.pdf';

        $pdf = new FiscalYearReportPdf(
            year: $year,
            snapshotData: $snapshotData,
            transactions: $transactions,
            locale: $locale,
        );

        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    private static function generateAnnualReportPdf(int $year, array $snapshot, Collection $transactions, ?string $filename, string $locale): string
    {
        $filename = $filename ?? "Jahresbericht-{$year}-".now()->format('Ymd').'.pdf';
        $pdf = new AnnualReportPdf($year, $snapshot, $transactions, $locale);
        $pdf->generateContent();

        return $pdf->Output($filename, 'S');
    }

    public static function generateMembershipApplication(): RedirectResponse
    {
        return redirect()->route('home');
    }
}
