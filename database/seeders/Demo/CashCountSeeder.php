<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\CashCount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

final class CashCountSeeder extends Seeder
{
    public function run(): void
    {
        $cashAccount = Account::where('type', AccountType::cash->value)->first();

        if (! $cashAccount) {
            return;
        }

        $auditor = User::first();

        // Nur vergangene Monate (draft = aktueller Monat bekommt keine Zählung)
        $reports = AccountReport::where('account_id', $cashAccount->id)
            ->where('period_end', '<', now()->startOfMonth()->toDateString())
            ->orderBy('period_end')
            ->take(3) // 2-3 Zählungen wie gewünscht
            ->get();

        foreach ($reports as $report) {
            $targetCents = (int) $report->end_amount;

            // Zähldatum: letzter Tag des Monats
            $countedAt = Carbon::parse($report->period_end);

            CashCount::create([
                'account_id' => $cashAccount->id,
                'user_id'    => $auditor->id,
                'counted_at' => $countedAt,
                'label'      => 'Kassensturz '.$countedAt->isoFormat('MMMM YYYY'),
                'notes'      => null,
                ...$this->denominationsFor($targetCents),
            ]);
        }
    }

    /**
     * Zerlegt einen Cent-Betrag in realistische Schein- und Münzkombinationen.
     * Arbeitet von groß nach klein (greedy), spart große Scheine wenn möglich.
     */
    private function denominationsFor(int $cents): array
    {
        // Denomination => Feldname => Wert in Cent
        $denominations = [
            'euro_two_hundred' => 20000,
            'euro_one_hundred' => 10000,
            'euro_fifty'       => 5000,
            'euro_twenty'      => 2000,
            'euro_ten'         => 1000,
            'euro_five'        => 500,
            'euro_two'         => 200,
            'euro_one'         => 100,
            'cent_fifty'       => 50,
            'cent_twenty'      => 20,
            'cent_ten'         => 10,
            'cent_five'        => 5,
            'cent_two'         => 2,
            'cent_one'         => 1,
        ];

        $result    = [];
        $remaining = $cents;

        foreach ($denominations as $field => $value) {
            $count          = (int) floor($remaining / $value);
            $result[$field] = $count;
            $remaining     -= $count * $value;
        }

        return $result;
    }
}