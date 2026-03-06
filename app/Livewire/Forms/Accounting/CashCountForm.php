<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Report\CreateCashCountReport;
use App\Models\Accounting\CashCount;
use Livewire\Form;

final class CashCountForm extends Form
{
    public CashCount $cashCount;

    public int $account_id;

    public int $user_id;

    public string $counted_at;

    public string $label;

    public string $notes;

    public int $euro_two_hundred;

    public int $euro_one_hundred;

    public int $euro_fifty;

    public int $euro_twenty;

    public int $euro_ten;

    public int $euro_five;

    public int $euro_two;

    public int $euro_one;

    public int $cent_fifty;

    public int $cent_twenty;

    public int $cent_ten;

    public int $cent_five;

    public int $cent_two;

    public int $cent_one;

    public function set(int $id): void
    {
        $this->cashCount = CashCount::query()->findOrFail($id);
        $this->counted_at = $this->cashCount->counted_at->format('Y-m-d');
        $this->account_id = $this->cashCount->account_id;
        $this->label = $this->cashCount->label;
        $this->notes = $this->cashCount->notes;
        $this->user_id = $this->cashCount->user_id;
        $this->euro_two_hundred = $this->cashCount->euro_two_hundred;
        $this->euro_one_hundred = $this->cashCount->euro_one_hundred;
        $this->euro_fifty = $this->cashCount->euro_fifty;
        $this->euro_twenty = $this->cashCount->euro_twenty;
        $this->euro_ten = $this->cashCount->euro_ten;
        $this->euro_five = $this->cashCount->euro_five;
        $this->euro_two = $this->cashCount->euro_two;
        $this->euro_one = $this->cashCount->euro_one;
        $this->cent_fifty = $this->cashCount->cent_fifty;
        $this->cent_twenty = $this->cashCount->cent_twenty;
        $this->cent_ten = $this->cashCount->cent_ten;
        $this->cent_five = $this->cashCount->cent_five;
        $this->cent_two = $this->cashCount->cent_two;
        $this->cent_one = $this->cashCount->cent_one;
    }

    public function init(): void
    {
        $this->notes = '';
        $this->euro_two_hundred = 0;
        $this->euro_one_hundred = 0;
        $this->euro_fifty = 0;
        $this->euro_twenty = 0;
        $this->euro_ten = 0;
        $this->euro_five = 0;
        $this->euro_two = 0;
        $this->euro_one = 0;
        $this->cent_fifty = 0;
        $this->cent_twenty = 0;
        $this->cent_ten = 0;
        $this->cent_five = 0;
        $this->cent_two = 0;
        $this->cent_one = 0;
    }

    public function create(): CashCount
    {
        $this->validate();

        return CreateCashCountReport::handle($this);
    }

    public function update(): void {}

    protected function rules(): array
    {
        return [
            'account_id' => 'required',
            'user_id' => 'required',
            'label' => 'required|string',
            'notes' => 'nullable|string',
            'counted_at' => 'required|date',
            'euro_two_hundred' => 'nullable|integer',
            'euro_one_hundred' => 'nullable|integer',
            'euro_fifty' => 'nullable|integer',
            'euro_twenty' => 'nullable|integer',
            'euro_ten' => 'nullable|integer',
            'euro_five' => 'nullable|integer',
            'euro_two' => 'nullable|integer',
            'euro_one' => 'nullable|integer',
            'cent_fifty' => 'nullable|integer',
            'cent_twenty' => 'nullable|integer',
            'cent_ten' => 'nullable|integer',
            'cent_five' => 'nullable|integer',
            'cent_two' => 'nullable|integer',
            'cent_one' => 'nullable|integer',

        ];
    }
}
