<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\Models\Accounting\Account;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Crypt;

final class SepaSettingsService
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    public function creditorId(): string
    {
        return (string) $this->settings->get('sepa.creditor_id', '');
    }

    public function setCreditorId(string $value): void
    {
        $this->settings->set('sepa.creditor_id', $value);
    }

    public function creditorAccountId(): ?int
    {
        $id = $this->settings->get('sepa.creditor_account_id');

        return $id !== null ? (int) $id : null;
    }

    public function setCreditorAccountId(?int $value): void
    {
        $this->settings->set('sepa.creditor_account_id', $value, 'integer');
    }

    public function creditorAccount(): ?Account
    {
        $id = $this->creditorAccountId();

        return $id !== null ? Account::find($id) : null;
    }

    public function dueDateOffset(): int
    {
        return (int) $this->settings->get('sepa.due_date_offset', 5);
    }

    public function setDueDateOffset(int $value): void
    {
        $this->settings->set('sepa.due_date_offset', $value, 'integer');
    }

    public function painFormat(): string
    {
        return (string) $this->settings->get('sepa.pain_format', 'pain.008.001.09');
    }

    public function setPainFormat(string $value): void
    {
        $this->settings->set('sepa.pain_format', $value);
    }

    public function transferMode(): string
    {
        return (string) $this->settings->get('sepa.transfer_mode', 'manual');
    }

    public function setTransferMode(string $value): void
    {
        $this->settings->set('sepa.transfer_mode', $value);
    }

    public function ebicsHost(): string
    {
        return (string) $this->settings->get('sepa.ebics_host', '');
    }

    public function setEbicsHost(string $value): void
    {
        $this->settings->set('sepa.ebics_host', $value);
    }

    public function ebicsHostId(): string
    {
        return (string) $this->settings->get('sepa.ebics_host_id', '');
    }

    public function setEbicsHostId(string $value): void
    {
        $this->settings->set('sepa.ebics_host_id', $value);
    }

    public function ebicsPartnerId(): string
    {
        return (string) $this->settings->get('sepa.ebics_partner_id', '');
    }

    public function setEbicsPartnerId(string $value): void
    {
        $this->settings->set('sepa.ebics_partner_id', $value);
    }

    public function ebicsUserId(): string
    {
        return (string) $this->settings->get('sepa.ebics_user_id', '');
    }

    public function setEbicsUserId(string $value): void
    {
        $this->settings->set('sepa.ebics_user_id', $value);
    }

    public function ebicsPassphrase(): string
    {
        $value = $this->settings->get('sepa.ebics_passphrase', '');

        if ($value === '') {
            return '';
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setEbicsPassphrase(string $value): void
    {
        $this->settings->set('sepa.ebics_passphrase', Crypt::encryptString($value));
    }

    public function isEbicsConfigured(): bool
    {
        return $this->ebicsHost() !== ''
            && $this->ebicsHostId() !== ''
            && $this->ebicsPartnerId() !== ''
            && $this->ebicsUserId() !== ''
            && $this->ebicsPassphrase() !== '';
    }

    public function isConfigured(): bool
    {
        return $this->creditorId() !== ''
            && $this->creditorAccountId() !== null;
    }

    public function toArray(): array
    {
        return [
            'creditor_id' => $this->creditorId(),
            'creditor_account_id' => $this->creditorAccountId(),
            'due_date_offset' => $this->dueDateOffset(),
            'pain_format' => $this->painFormat(),
            'transfer_mode' => $this->transferMode(),
            'ebics_host' => $this->ebicsHost(),
            'ebics_host_id' => $this->ebicsHostId(),
            'ebics_partner_id' => $this->ebicsPartnerId(),
            'ebics_user_id' => $this->ebicsUserId(),
            'ebics_passphrase' => $this->ebicsPassphrase(),
            'is_configured' => $this->isConfigured(),
            'is_ebics_configured' => $this->isEbicsConfigured(),
        ];
    }
}
