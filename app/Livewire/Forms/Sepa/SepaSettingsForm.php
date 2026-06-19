<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Sepa;

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use App\Services\Sepa\SepaSettingsService;
use Livewire\Form;

class SepaSettingsForm extends Form
{
    public string $creditor_id = '';

    public ?int $creditor_account_id = null;

    public int $due_date_offset = 5;

    public string $pain_format = 'pain.008.001.09';

    public string $transfer_mode = 'manual';

    public string $ebics_host = '';

    public string $ebics_host_id = '';

    public string $ebics_partner_id = '';

    public string $ebics_user_id = '';

    public string $ebics_passphrase = '';

    public function rules(): array
    {
        $ebicsRequired = $this->transfer_mode === 'ebics' ? 'required' : 'nullable';

        return [
            'creditor_id' => ['required', 'string', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{2,25}$/'],
            'creditor_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'due_date_offset' => ['required', 'integer', 'min:1', 'max:30'],
            'pain_format' => ['required', 'string', 'in:pain.008.001.02,pain.008.001.09,pain.008.003.01'],
            'transfer_mode' => ['required', 'string', 'in:manual,ebics'],
            'ebics_host' => [$ebicsRequired, 'string', 'max:255'],
            'ebics_host_id' => [$ebicsRequired, 'string', 'max:255'],
            'ebics_partner_id' => [$ebicsRequired, 'string', 'max:255'],
            'ebics_user_id' => [$ebicsRequired, 'string', 'max:255'],
            'ebics_passphrase' => [$ebicsRequired, 'string', 'max:1024'],
        ];
    }

    public function load(): void
    {
        /** @var SepaSettingsService $sepaSettings */
        $sepaSettings = app(SepaSettingsService::class);

        $this->creditor_id = $sepaSettings->creditorId();
        $this->creditor_account_id = $sepaSettings->creditorAccountId();
        $this->due_date_offset = $sepaSettings->dueDateOffset();
        $this->pain_format = $sepaSettings->painFormat();
        $this->transfer_mode = $sepaSettings->transferMode();
        $this->ebics_host = $sepaSettings->ebicsHost();
        $this->ebics_host_id = $sepaSettings->ebicsHostId();
        $this->ebics_partner_id = $sepaSettings->ebicsPartnerId();
        $this->ebics_user_id = $sepaSettings->ebicsUserId();
        $this->ebics_passphrase = $sepaSettings->ebicsPassphrase();
    }

    public function save(SepaSettingsService $sepaSettings): void
    {
        $sepaSettings->setCreditorId($this->creditor_id);
        $sepaSettings->setCreditorAccountId($this->creditor_account_id);
        $sepaSettings->setDueDateOffset($this->due_date_offset);
        $sepaSettings->setPainFormat($this->pain_format);
        $sepaSettings->setTransferMode($this->transfer_mode);
        $sepaSettings->setEbicsHost($this->ebics_host);
        $sepaSettings->setEbicsHostId($this->ebics_host_id);
        $sepaSettings->setEbicsPartnerId($this->ebics_partner_id);
        $sepaSettings->setEbicsUserId($this->ebics_user_id);
        $sepaSettings->setEbicsPassphrase($this->ebics_passphrase);
    }

    public function bankAccounts(): array
    {
        return Account::query()
            ->where('type', AccountType::bank)
            ->orderBy('name')
            ->get()
            ->map(fn (Account $a) => [
                'id' => $a->id,
                'label' => $a->name.' — '.($a->iban ?: $a->number),
                'missingIban' => is_null($a->iban),
            ])
            ->toArray();
    }
}
