<?php

declare(strict_types=1);

namespace App\Livewire\App\Branding;

use App\Enums\FeeInterval;
use App\Models\Accounting\Account;
use App\Services\FeeService;
use App\Services\SettingsService;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FeeSettings extends Component
{

    public string $feePerYear = '';
    // Amounts in Cent
    #[Validate('required|integer|min:0')]
    public int $fullAmount = 500;

    #[Validate('required|integer|min:0')]
    public int $discountedAmount = 300;

    // Interval
    #[Validate('required|string|in:monthly,quarterly,biannual,yearly,custom')]
    public string $interval = 'yearly';

    // Custom interval
    #[Validate('nullable|integer|min:1|max:365')]
    public ?int $intervalN = 1;

    #[Validate('nullable|string|in:d,m,y')]
    public ?string $intervalUnit = 'y';

    public function mount(SettingsService $settings): void
    {
        $this->fullAmount = (int) $settings->get('fees.full_amount', 500);
        $this->discountedAmount = (int) $settings->get('fees.discounted_amount', 300);
        $this->interval = (string) $settings->get('fees.interval', 'yearly');
        $this->intervalN = (int) $settings->get('fees.interval_n', 1);
        $this->intervalUnit = (string) $settings->get('fees.interval_unit', 'y');

        $this->updatedInterval();
    }

    public function save(FeeService $feeService): void
    {
        $this->validate();

        $feeService->saveSettings([
            'full_amount' => $this->fullAmount,
            'discounted_amount' => $this->discountedAmount,
            'interval' => $this->interval,
            'interval_n' => $this->intervalN ?? 1,
            'interval_unit' => $this->intervalUnit ?? 'y',
        ]);

        $this->dispatch('saved');
    }

    public function updatedInterval(): void
    {

       $interval= FeeInterval::getYearlyMultiplier($this->interval);
       $fullAmount = (int) $this->fullAmount;

        $this->calcYearlyFee($fullAmount, $interval);
    }
    public function updatedIntervalN():void
    {
        $this->calcYearlyFee($this->fullAmount, $this->intervalN);
    }
    public function updatedIntervalUnit():void
    {

    }

    private function calcYearlyFee(int $fullAmount, int $interval): void
    {
        $this->feePerYear = Account::formatedAmount($fullAmount * $interval);
    }

    public function intervalOptions(): array
    {
        return FeeInterval::options();
    }

    public function isCustomInterval(): bool
    {
        return $this->interval === FeeInterval::CUSTOM->value;
    }

    public function render(): View
    {
        return view('livewire.app.branding.fee-settings');
    }
}
