<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

use App\Enums\AccountType;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\DatevSettingsService;
use Illuminate\Database\Eloquent\Collection;

final class DatevExportValidator
{
    public function __construct(
        private readonly DatevSettingsService $settings,
    ) {}

    /**
     * Führt alle Pre-Flight-Checks für einen Monatsbericht-Export durch.
     *
     * @return DatevCheck[]
     */
    public function validateForReport(AccountReport $report): array
    {
        $checks = [];

        $checks[] = $this->checkBeraterNr();
        $checks[] = $this->checkMandantNr();
        $checks[] = $this->checkKontoLaenge();
        $checks[] = $this->checkReportAudited($report);
        $checks[] = $this->checkHasTransactions($report);
        $checks[] = $this->checkBookingAccountIds($report);
        $checks[] = $this->checkAccountTypes($report);
        $checks[] = $this->checkBookingAccountAreas($report);
        $checks[] = $this->checkBookingAccountNumbers($report);

        return $checks;
    }

    private function checkBeraterNr(): DatevCheck
    {
        $nr = $this->settings->beraterNr();
        $passed = $nr !== '0000' && is_numeric($nr) && strlen($nr) >= 4 && strlen($nr) <= 7;

        return new DatevCheck(
            label: 'Beraternummer',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : 'Die Beraternummer muss zwischen 4 und 7 Stellen haben. Hinterlege sie in den DATEV-Einstellungen.',
        );
    }

    private function checkMandantNr(): DatevCheck
    {
        $nr = $this->settings->mandantNr();
        $passed = $nr !== '00000' && is_numeric($nr) && strlen($nr) <= 5;

        return new DatevCheck(
            label: 'Mandantennummer',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : 'Die Mandantennummer muss zwischen 1 und 5 Stellen haben. Hinterlege sie in den DATEV-Einstellungen.',
        );
    }

    private function checkKontoLaenge(): DatevCheck
    {
        $laenge = $this->settings->kontoLaenge();
        $passed = in_array($laenge, [4, 5, 6, 7], true);

        return new DatevCheck(
            label: 'Sachkontenlänge',
            type: DatevCheckType::Warning,
            passed: $passed,
            message: $passed ? '' : "Die Sachkontenlänge {$laenge} ist unüblich. Erlaubt sind 4–7 Stellen.",
        );
    }

    private function checkReportAudited(AccountReport $report): DatevCheck
    {
        $passed = $report->status === \App\Enums\ReportStatus::audited;

        return new DatevCheck(
            label: 'Bericht geprüft',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : 'Der Bericht muss zuerst geprüft (auditiert) werden, bevor ein Export möglich ist.',
        );
    }

    private function checkHasTransactions(AccountReport $report): DatevCheck
    {
        $count = Transaction::query()
            ->where('account_id', $report->account_id)
            ->datevExportable()
            ->whereBetween('date', [
                $report->period_start->startOfDay(),
                $report->period_end->endOfDay(),
            ])
            ->count();

        $passed = $count > 0;

        return new DatevCheck(
            label: 'Buchungen vorhanden',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : 'Dieser Bericht enthält keine gebuchten Transaktionen. Ein DATEV-Export ist nicht möglich.',
        );
    }

    private function checkBookingAccountIds(AccountReport $report): DatevCheck
    {
        $missing = Transaction::query()
            ->where('account_id', $report->account_id)
            ->financialReportable()
            ->whereNull('booking_account_id')
            ->whereBetween('date', [
                $report->period_start->startOfDay(),
                $report->period_end->endOfDay(),
            ])
            ->count();

        $passed = $missing === 0;

        return new DatevCheck(
            label: 'Buchungskonten zugewiesen',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : "{$missing} Transaktionen haben kein Buchungskonto (booking_account_id). Weise ihnen ein SKR42-Konto zu.",
        );
    }

    private function checkAccountTypes(AccountReport $report): DatevCheck
    {
        $transactions = $this->loadTransactions($report);
        $unknownTypes = [];

        foreach ($transactions as $transaction) {
            $account = $transaction->account;
            if (! in_array($account->type, [AccountType::cash, AccountType::bank, AccountType::paypal], true)) {
                $unknownTypes[] = $transaction->id;
            }
        }

        $passed = $unknownTypes === [];

        return new DatevCheck(
            label: 'Geldkonten-Typen bekannt',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : count($unknownTypes).' Transaktionen haben einen unbekannten Kontotyp (nicht Kasse/Bank/PayPal).',
        );
    }

    private function checkBookingAccountAreas(AccountReport $report): DatevCheck
    {
        $transactions = $this->loadTransactions($report);
        $missingAreas = [];

        foreach ($transactions as $transaction) {
            $bookingAccount = $transaction->bookingAccount;
            if ($bookingAccount === null) {
                continue;
            }
            $area = $transaction->getAttribute('area') ?? $bookingAccount->getAttribute('area');
            if ($area === null) {
                $missingAreas[] = $transaction->id;
            }
        }

        $passed = $missingAreas === [];

        return new DatevCheck(
            label: 'Steuerliche Sphäre (KOST1)',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : count($missingAreas).' Buchungskonten haben keine steuerliche Sphäre (area). Der Export kann keine KOST1-Werte erzeugen.',
        );
    }

    private function checkBookingAccountNumbers(AccountReport $report): DatevCheck
    {
        $transactions = $this->loadTransactions($report);
        $invalidNumbers = [];

        foreach ($transactions as $transaction) {
            $bookingAccount = $transaction->bookingAccount;
            if ($bookingAccount === null) {
                continue;
            }
            $number = ltrim($bookingAccount->number, '0');
            if ($number === '' || !ctype_digit($number)) {
                $invalidNumbers[] = $transaction->id;
            }
        }

        $passed = $invalidNumbers === [];

        return new DatevCheck(
            label: 'Kontonummern gültig',
            type: DatevCheckType::Error,
            passed: $passed,
            message: $passed ? '' : count($invalidNumbers).' Buchungskonten haben ungültige (nicht-numerische) Kontonummern.',
        );
    }

    /**
     * @return Collection<int, Transaction>
     */
    private function loadTransactions(AccountReport $report): Collection
    {
        return Transaction::query()
            ->with(['bookingAccount', 'account'])
            ->where('account_id', $report->account_id)
            ->datevExportable()
            ->whereBetween('date', [
                $report->period_start->startOfDay(),
                $report->period_end->endOfDay(),
            ])
            ->get();
    }
}
