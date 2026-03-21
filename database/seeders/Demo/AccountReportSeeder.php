<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\AccountType;
use App\Enums\MemberType;
use App\Enums\ReportStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class AccountReportSeeder extends Seeder
{
    private Collection $auditors;

    public function run(): void
    {
        // Kassenprüfer: Vorstand (MD) und Beirat (AD) mit verknüpftem User-Account
        $this->auditors = Member::whereIn('type', [
            MemberType::MD->value,
            MemberType::AD->value,
        ])
            ->whereNotNull('user_id')
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        // Fallback: erster User falls keine passenden Members mit User verknüpft sind
        if ($this->auditors->isEmpty()) {
            $this->auditors = User::take(1)->get();
        }

        $accounts = Account::whereIn('type', [
            AccountType::cash->value,
            AccountType::paypal->value,
            AccountType::bank->value,
        ])->get();

        foreach ($accounts as $account) {
            $this->seedReportsForAccount($account);
        }
    }

    private function seedReportsForAccount(Account $account): void
    {
        // Konto ohne Transaktionen überspringen
        if (! Transaction::where('account_id', $account->id)->exists()) {
            return;
        }

        $months = collect(range(0, 5))
            ->map(fn ($i) => now()->subMonths($i)->startOfMonth())
            ->reverse()
            ->values();

        $runningBalance = $account->starting_amount ?? 0;

        foreach ($months as $month) {
            /** @var Carbon $month */
            $periodStart = $month->copy()->startOfMonth();
            $periodEnd = $month->copy()->endOfMonth();

            $transactions = Transaction::query()
                ->where('account_id', $account->id)
                ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->get();

            $totalIncome = 0;
            $totalExpenditure = 0;

            foreach ($transactions as $transaction) {
                $amount = (int) ($transaction->amount_gross ?? 0);

                if ($transaction->type === TransactionType::Deposit) {
                    $totalIncome += $amount;
                } else {
                    $totalExpenditure += $amount;
                }
            }

            $endAmount = $runningBalance + $totalIncome - $totalExpenditure;
            $isPastMonth = $periodEnd->lt(now()->startOfMonth());

            $report = AccountReport::create([
                'account_id' => $account->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'starting_amount' => $runningBalance,
                'end_amount' => $endAmount,
                'total_income' => $totalIncome,
                'total_expenditure' => $totalExpenditure,
                'status' => $isPastMonth
                    ? ReportStatus::audited->value
                    : ReportStatus::draft->value,
                'created_by' => 1,
            ]);

            // Vergangene Monate bekommen einen genehmigten Audit-Eintrag
            if ($isPastMonth) {
                $this->createAudit($report);
            }

            $runningBalance = $endAmount;
        }
    }

    private function createAudit(AccountReport $report): void
    {
        // Zufälligen Kassenprüfer aus MD/AD wählen
        $auditor = $this->auditors->random();

        AccountReportAudit::create([
            'account_report_id' => $report->id,
            'user_id' => $auditor->id,
            'is_approved' => true,
            'approved_at' => Carbon::parse($report->period_end)
                ->addDays(rand(3, 10)) // realistisch: Prüfung einige Tage nach Monatsende
                ->toDateTimeString(),
            'reason' => null,
        ]);
    }
}
