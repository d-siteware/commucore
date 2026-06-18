<?php

declare(strict_types=1);

use App\Models\Accounting\Account;
use App\Services\Sepa\SepaSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function settingsService(): SepaSettingsService
{
    return app(SepaSettingsService::class);
}

describe('creditor settings', function (): void {

    it('returns default values when nothing is configured', function (): void {
        expect(settingsService()->creditorId())->toBe('');
        expect(settingsService()->painFormat())->toBe('pain.008.001.09');
        expect(settingsService()->dueDateOffset())->toBe(5);
        expect(settingsService()->transferMode())->toBe('manual');
        expect(settingsService()->ebicsHost())->toBe('');
    });

    it('sets and gets creditor ID', function (): void {
        settingsService()->setCreditorId('DE00ZZZ00000000000');

        expect(settingsService()->creditorId())->toBe('DE00ZZZ00000000000');
    });

    it('sets and gets pain format', function (): void {
        settingsService()->setPainFormat('pain.008.003.01');

        expect(settingsService()->painFormat())->toBe('pain.008.003.01');
    });

    it('sets and gets due date offset', function (): void {
        settingsService()->setDueDateOffset(10);

        expect(settingsService()->dueDateOffset())->toBe(10);
    });

    it('sets and gets transfer mode', function (): void {
        settingsService()->setTransferMode('ebics');

        expect(settingsService()->transferMode())->toBe('ebics');
    });

    it('sets and gets creditor account', function (): void {
        $account = Account::factory()->create();

        settingsService()->setCreditorAccountId($account->id);

        expect(settingsService()->creditorAccountId())->toBe($account->id);
        expect(settingsService()->creditorAccount())->toBeInstanceOf(Account::class);
        expect(settingsService()->creditorAccount()->id)->toBe($account->id);
    });

    it('returns null creditor account when not configured', function (): void {
        expect(settingsService()->creditorAccount())->toBeNull();
    });

});

describe('EBICS settings', function (): void {

    it('sets and gets all EBICS fields', function (): void {
        settingsService()->setEbicsHost('https://ebics.bank.de/ebics/ebics.aspx');
        settingsService()->setEbicsHostId('HOSTID');
        settingsService()->setEbicsPartnerId('PARTNER01');
        settingsService()->setEbicsUserId('USER001');
        settingsService()->setEbicsPassphrase('secret123');

        expect(settingsService()->ebicsHost())->toBe('https://ebics.bank.de/ebics/ebics.aspx');
        expect(settingsService()->ebicsHostId())->toBe('HOSTID');
        expect(settingsService()->ebicsPartnerId())->toBe('PARTNER01');
        expect(settingsService()->ebicsUserId())->toBe('USER001');
        expect(settingsService()->ebicsPassphrase())->toBe('secret123');
    });

});

describe('configuration checks', function (): void {

    it('isConfigured returns false when incomplete', function (): void {
        expect(settingsService()->isConfigured())->toBeFalse();
    });

    it('isConfigured returns true when creditor settings exist', function (): void {
        $account = Account::factory()->create();
        settingsService()->setCreditorId('DE00ZZZ00000000000');
        settingsService()->setCreditorAccountId($account->id);

        expect(settingsService()->isConfigured())->toBeTrue();
    });

    it('isEbicsConfigured returns false when incomplete', function (): void {
        expect(settingsService()->isEbicsConfigured())->toBeFalse();
    });

    it('isEbicsConfigured returns true when all EBICS fields are set', function (): void {
        settingsService()->setEbicsHost('https://ebics.bank.de');
        settingsService()->setEbicsHostId('HID');
        settingsService()->setEbicsPartnerId('PID');
        settingsService()->setEbicsUserId('UID');
        settingsService()->setEbicsPassphrase('pw');

        expect(settingsService()->isEbicsConfigured())->toBeTrue();
    });

    it('toArray returns all settings', function (): void {
        settingsService()->setCreditorId('DE00ZZZ00000000000');

        $data = settingsService()->toArray();

        expect($data)->toBeArray();
        expect($data['creditor_id'])->toBe('DE00ZZZ00000000000');
        expect($data)->toHaveKey('pain_format');
        expect($data)->toHaveKey('transfer_mode');
        expect($data)->toHaveKey('ebics_host');
    });

});
