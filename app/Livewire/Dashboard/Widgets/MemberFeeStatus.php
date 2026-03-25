<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Enums\MemberFeeType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use Illuminate\Support\Carbon;
use Livewire\Component;

class MemberFeeStatus extends Component
{
    public int $year;

    /** Soll-Betrag in Cent */
    public int $targetAmount = 0;

    /** Ist-Betrag (gebucht) in Cent */
    public int $bookedAmount = 0;

    /** Eingereicht aber noch nicht gebucht, in Cent */
    public int $submittedAmount = 0;

    public function mount(): void
    {
        $this->year = Carbon::now('Europe/Berlin')->year;
        $this->loadData();
    }

    private function loadData(): void
    {
        $activeTypes = [MemberType::MD->value, MemberType::AD->value];

        // Soll: Jahresbeitrag jedes zahlungspflichtigen aktiven Mitglieds
        // Kein Raw-SQL nötig – läuft komplett in PHP über die Enum-Methode
        $this->targetAmount = Member::query()
            ->whereIn('type', $activeTypes)
            ->whereNotNull('entered_at')
            ->whereNull('left_at')
            ->where('fee_type', '!=', MemberFeeType::FREE->value)
            ->get(['id', 'fee_type'])
            ->sum(fn (Member $m): int => $m->fee_type->fee() * 12);

        // Ist (gebucht) & Eingereicht: JOIN auf transactions – kein datumsspezifischer Raw-SQL nötig,
        // da fee_year als eigene Spalte in member_transactions existiert
        $this->bookedAmount = MemberTransaction::query()
            ->membershipFees()
            ->forYear($this->year)
            ->paid()
            ->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
            ->sum('transactions.amount_net');

        $this->submittedAmount = MemberTransaction::query()
            ->membershipFees()
            ->forYear($this->year)
            ->submitted()
            ->join('transactions', 'member_transactions.transaction_id', '=', 'transactions.id')
            ->sum('transactions.amount_net');
    }

    /** Prozentualer Anteil gebucht (0–100, gecapped) */
    public function bookedRate(): float
    {
        if ($this->targetAmount === 0) {
            return 0.0;
        }

        return min(100.0, round($this->bookedAmount / $this->targetAmount * 100, 1));
    }

    /** Prozentualer Anteil submitted on top (0–100, gecapped auf Gesamtbalken) */
    public function submittedRate(): float
    {
        if ($this->targetAmount === 0) {
            return 0.0;
        }

        $total = min(100.0, round(($this->bookedAmount + $this->submittedAmount) / $this->targetAmount * 100, 1));

        return max(0.0, $total - $this->bookedRate());
    }

    public function render()
    {
        return view('livewire.dashboard.widgets.member-fee-status');
    }
}