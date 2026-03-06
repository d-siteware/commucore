<?php

declare(strict_types=1);

use App\Models\Accounting\AccountReport;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\Role;
use App\Models\User;
use App\Services\PdfGeneratorService;
use Database\Seeders\Demo\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {

    // Seede die Rollen richtig mit allen benötigten Daten
    $this->seed(RoleSeeder::class);

    // Erstelle Board Members für jede Rolle
    $roles = Role::all();
    foreach ($roles as $role) {
        $member = Member::factory()->create([
            'type' => \App\Enums\MemberType::MD->value,
        ]);

        $role->members()->attach($member->id, [
            'designated_at' => now(),
            'resigned_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
});

test('member application pdf can be generated', function (): void {
    $member = Member::factory()
        ->create([
            'first_name' => 'Janos',
            'name' => 'Kovacs',
            'email' => 'janos@example.com',
            'fee_type' => \App\Enums\MemberFeeType::FULL,
        ]);

    ob_start();
    $pdfContent = PdfGeneratorService::generatePdf('member-application', $member);
    ob_end_clean();
    expect($pdfContent)
        ->toBeString()
        ->toStartWith('%PDF-')
        ->and(strlen($pdfContent))
        ->toBeGreaterThan(1000); // Reasonable size
});

test('invoice pdf generation requires authentication', function (): void {
    $transaction = Transaction::factory()
        ->create(['amount_gross' => 10000]);
    $user = User::factory()->create(['email_verified_at' => now()]);
    $member = Member::factory()->withUser()->create(['user_id' => $user->id]);

    // Without auth
    expect(fn (): string => PdfGeneratorService::generatePdf('invoice', ['transaction' => $transaction, 'member' => $member], null, true))
        ->toThrow(\Exception::class, 'Authentication required to generate this PDF.');

    $this->actingAs($user);

    ob_start();
    $pdfContent = PdfGeneratorService::generatePdf('invoice', ['transaction' => $transaction, 'member' => $member], null, true);
    ob_end_clean();
    expect($pdfContent)
        ->toBeString()
        ->toStartWith('%PDF-')
        ->and(strlen($pdfContent))
        ->toBeGreaterThan(1000);
});

test('event report pdf can be generated', function (): void {
    $event = \App\Models\Event\Event::factory()
        ->create();
    $user = User::factory()->create(['email_verified_at' => now()]);
    $member = Member::factory()->withUser()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    ob_start();
    $pdfContent = PdfGeneratorService::generatePdf('event-report', $event, null, true);
    ob_end_clean();
    expect($pdfContent)
        ->toBeString()
        ->toStartWith('%PDF-')
        ->and(strlen($pdfContent))
        ->toBeGreaterThan(1000);
});

test('account report pdf can be generated', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $member = Member::factory()->withUser()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $report = AccountReport::factory()
        ->create(['period_start' => now(), 'created_by' => $member->user->id]);

    // Auth for restricted
    ob_start();
    $pdfContent = PdfGeneratorService::generatePdf('account-report', $report, null, true);
    ob_end_clean();
    expect($pdfContent)
        ->toBeString()
        ->toStartWith('%PDF-')
        ->and(strlen($pdfContent))
        ->toBeGreaterThan(1000);
});
