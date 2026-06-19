<?php

declare(strict_types=1);

use App\Enums\MemberFeeType;
use App\Enums\SepaSequenceType;
use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Services\Sepa\SepaDirectDebitService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

function dueDate(): Carbon
{
    return Carbon::now()->addDays(5);
}

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

        $debits = [
            [
                'member' => $frstMember,
                'mandate' => $frstMandate,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 2026',
                'endToEndId' => 'E2E-FRST-001',
                'sequenceType' => SepaSequenceType::Frst,
            ],
            [
                'member' => $rcurMember,
                'mandate' => $rcurMandate,
                'amount' => 12000,
                'remittanceInformation' => 'Beitrag 2026',
                'endToEndId' => 'E2E-RCUR-001',
                'sequenceType' => SepaSequenceType::Rcur,
            ],
        ];

        $xml = debitService()->generateBatch(
            debits: $debits,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
            dueDate: dueDate(),
            painFormat: 'pain.008.001.02',
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
        $mandate = SepaMandate::factory()->for($member)->create([
            'last_used_at' => null,
        ]);

        $debits = [
            [
                'member' => $member,
                'mandate' => $mandate,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 2026',
                'endToEndId' => 'E2E-FRST-001',
                'sequenceType' => SepaSequenceType::Frst,
            ],
        ];

        $xml = debitService()->generateBatch(
            debits: $debits,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
            dueDate: dueDate(),
            painFormat: 'pain.008.001.02',
        );

        expect($xml)->toContain('FRST');
        expect($xml)->not->toContain('RCUR');
    });

    it('generates XML with only RCUR group', function (): void {
        $account = creditAccount();

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create([
            'last_used_at' => now()->subYear(),
        ]);

        $debits = [
            [
                'member' => $member,
                'mandate' => $mandate,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 2026',
                'endToEndId' => 'E2E-RCUR-001',
                'sequenceType' => SepaSequenceType::Rcur,
            ],
        ];

        $xml = debitService()->generateBatch(
            debits: $debits,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
            dueDate: dueDate(),
            painFormat: 'pain.008.001.02',
        );

        expect($xml)->toContain('RCUR');
        expect($xml)->not->toContain('FRST');
    });

    it('groups debits by sequence type and uses mandate from first debit per group', function (): void {
        $account = creditAccount();

        $member1 = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate1 = SepaMandate::factory()->for($member1)->b2b()->create([
            'last_used_at' => null,
        ]);

        $member2 = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate2 = SepaMandate::factory()->for($member2)->create([
            'last_used_at' => null,
        ]);

        $debits = [
            [
                'member' => $member1,
                'mandate' => $mandate1,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 1',
                'endToEndId' => 'E2E-001',
                'sequenceType' => SepaSequenceType::Frst,
            ],
            [
                'member' => $member2,
                'mandate' => $mandate2,
                'amount' => 6000,
                'remittanceInformation' => 'Beitrag 2',
                'endToEndId' => 'E2E-002',
                'sequenceType' => SepaSequenceType::Frst,
            ],
        ];

        $xml = debitService()->generateBatch(
            debits: $debits,
            creditorAccount: $account,
            creditorId: 'DE00ZZZ00000000000',
            dueDate: dueDate(),
            painFormat: 'pain.008.001.02',
        );

        expect($xml)->toContain('B2B');
    });

});
