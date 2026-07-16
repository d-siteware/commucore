<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Accounting\CreateBookingAccount;
use App\Enums\AccountCategory;
use App\Enums\AccountSubtype;
use App\Enums\BookingAccountArea;
use App\Models\Accounting\FiscalYear;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class BookingAccountForm extends Form
{
    public ?int $id = null;

    public string $number = '';

    public string $label = '';

    public string $category = '';

    public ?string $subtype = null;

    public string $area = '';

    public function create(): void
    {
        $this->validate();

        $typeId = FiscalYear::getActive()?->booking_account_type_id;

        $booking_account = CreateBookingAccount::create([
            'number' => $this->number,
            'label' => $this->label,
            'category' => $this->category,
            'subtype' => $this->subtype ?: null,
            'area' => $this->area,
            'booking_account_type_id' => $typeId,
        ]);

        Flux::toast(
            text: __('account.toast.booking_account_created.text'),
            heading: __('account.toast.booking_account_created.heading'),
            variant: 'success',
        );

        $this->id = $booking_account->id;

        Flux::modal('add-booking-account-modal')->close();
    }

    protected function rules(): array
    {
        $typeId = FiscalYear::getActive()?->booking_account_type_id;

        return [
            'number' => [
                'required',
                'string',
                Rule::unique('booking_accounts', 'number')
                    ->where('booking_account_type_id', $typeId),
            ],
            'label' => ['required', 'string'],
            'category' => ['required', Rule::enum(AccountCategory::class)],
            'subtype' => ['nullable', Rule::enum(AccountSubtype::class)],
            'area' => ['required', Rule::enum(BookingAccountArea::class)],
        ];
    }
}
