<?php

declare(strict_types=1);

use App\Enums\MemberFeeType;
use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Services\Sepa\SepaDirectDebitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function debitService(): SepaDirectDebitService
{
    return app(SepaDirectDebitService::class);
}

function creditAccount(): Account
{
    return Account::factory()->create([
        'name' => 'Testbank e.V.',
        'iban' => 'DE89370400440532013000',
        'bic' => 'COBADEFFXXX',
        'institute' => 'Testbank',
    ]);
}

describe('generateSingle', function (): void {

    it('generates pain.008 XML for a single member', function (): void {
        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create([
            'last_used_at' => null,
        ]);
        $account = creditAccount();

        $xml = debitService()->generateSingle(
            member: $member,
            amountCents: 6000,
            remittanceInformation: 'Mitgliedsbeitrag 2026',
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );

        expect($xml)->toBeString();
        expect($xml)->toContain('<?xml');
        expect($xml)->toContain('pain.008');
        expect($xml)->toContain($mandate->iban);
        expect($xml)->toContain($mandate->account_holder);
        expect($xml)->toContain($mandate->mandate_reference);
        expect($xml)->toContain('60.00');
        expect($xml)->toContain($account->name);
        expect($xml)->toContain('FRST');
    });

    it('uses RCUR sequence type for returning members', function (): void {
        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create([
            'last_used_at' => now()->subYear(),
        ]);
        $account = creditAccount();

        $xml = debitService()->generateSingle(
            member: $member,
            amountCents: 6000,
            remittanceInformation: 'Mitgliedsbeitrag 2026',
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );

        expect($xml)->toContain('RCUR');
        expect($xml)->not->toContain('FRST');
    });

    it('throws for members without active mandate', function (): void {
        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $account = creditAccount();

        debitService()->generateSingle(
            member: $member,
            amountCents: 6000,
            remittanceInformation: 'Test',
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );
    })->throws(RuntimeException::class, 'has no active SEPA mandate');

    it('marks the mandate as used after generation', function (): void {
        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create([
            'last_used_at' => null,
        ]);
        $account = creditAccount();

        expect($mandate->last_used_at)->toBeNull();

        debitService()->generateSingle(
            member: $member,
            amountCents: 6000,
            remittanceInformation: 'Test',
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );

        $mandate->refresh();
        expect($mandate->last_used_at)->not->toBeNull();
    });

});

describe('generateBatch', function (): void {

    it('generates batch XML for FRST and RCUR groups', function (): void {
        $account = creditAccount();

        $frstMember = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $frstMandate = SepaMandate::factory()->for($frstMember)->create([
            'last_used_at' => null,
        ]);

        $rcurMember = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $rcurMandate = SepaMandate::factory()->for($rcurMember)->create([
            'last_used_at' => now()->subYear(),
        ]);

        $transactions = [
            [
                'member' => $frstMember,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 2026',
                'endToEndId' => 'E2E-FRST-001',
            ],
            [
                'member' => $rcurMember,
                'amount' => 12000,
                'remittanceInformation' => 'Beitrag 2026',
                'endToEndId' => 'E2E-RCUR-001',
            ],
        ];

        $xml = debitService()->generateBatch(
            transactions: $transactions,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );

        expect($xml)->toBeString();
        expect($xml)->toContain('<?xml');
        expect($xml)->toContain('pain.008');
        expect($xml)->toContain('FRST');
        expect($xml)->toContain('RCUR');
        expect($xml)->toContain('60.00');
        expect($xml)->toContain('120.00');
    });

    it('generates XML with only FRST group', function (): void {
        $account = creditAccount();

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create([
            'last_used_at' => null,
        ]);

        $transactions = [
            [
                'member' => $member,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 2026',
            ],
        ];

        $xml = debitService()->generateBatch(
            transactions: $transactions,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );

        expect($xml)->toContain('FRST');
        expect($xml)->not->toContain('RCUR');
    });

    it('generates XML with only RCUR group', function (): void {
        $account = creditAccount();

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create([
            'last_used_at' => now()->subYear(),
        ]);

        $transactions = [
            [
                'member' => $member,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 2026',
            ],
        ];

        $xml = debitService()->generateBatch(
            transactions: $transactions,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );

        expect($xml)->toContain('RCUR');
        expect($xml)->not->toContain('FRST');
    });

    it('throws when a member has no active mandate', function (): void {
        $account = creditAccount();

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);

        $transactions = [
            [
                'member' => $member,
                'amount' => 6000,
                'remittanceInformation' => 'Test',
            ],
        ];

        debitService()->generateBatch(
            transactions: $transactions,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
        );
    })->throws(RuntimeException::class);

});

describe('generateAndStore', function (): void {

    it('generates and stores XML to disk', function (): void {
        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();
        $account = creditAccount();

        $path = debitService()->generateAndStore(
            member: $member,
            amountCents: 6000,
            remittanceInformation: 'Test',
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
            disk: 'local',
        );

        expect($path)->toBeString();
        expect($path)->toContain('sepa/');
        expect($path)->toContain('SEPA-');

        expect(Storage::disk('local')->exists($path))->toBeTrue();
    });

});
