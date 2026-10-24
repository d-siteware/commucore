<?php

declare(strict_types=1);

use App\Models\Event\EventVisitor;
use App\Models\Membership\Member;
use App\Models\Membership\Role;
use App\Models\User;
use App\Services\OnboardingStatusService;
use Database\Seeders\DatevSettingsSeeder;
use Database\Seeders\DefaultBookingAccountTypeSeeder;
use Database\Seeders\DemoSeeder;
use Database\Seeders\FundingPositionCategorySeeder;
use Database\Seeders\LocaleSeeder;
use Database\Seeders\SKR42BookingAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('seeds the demo onboarding requirements', function (): void {
    Cache::flush();
    Storage::fake('public');
    Role::factory()->create([
        'name' => ['de' => 'Revisor(in)', 'en' => 'Auditor', 'hu' => 'Könyvvizsgáló'],
        'can_manage_accounting' => true,
        'can_audit_accounting' => false,
    ]);

    $this->seed([
        LocaleSeeder::class,
        DefaultBookingAccountTypeSeeder::class,
        SKR42BookingAccountSeeder::class,
        DatevSettingsSeeder::class,
        FundingPositionCategorySeeder::class,
        DemoSeeder::class,
    ]);

    $status = app(OnboardingStatusService::class)->getStatus();

    expect($status['has_statute'])->toBeTrue()
        ->and($status['has_datev_berater_nr'])->toBeTrue()
        ->and($status['has_datev_mandant_nr'])->toBeTrue()
        ->and($status['has_all_roles_assigned'])->toBeTrue()
        ->and($status['has_logo'])->toBeTrue()
        ->and($status['has_about_us'])->toBeTrue();

    $auditorRole = Role::query()->where('name->de', 'Revisor(in)')->firstOrFail();
    expect($auditorRole->can_manage_accounting)->toBeFalse()
        ->and($auditorRole->can_audit_accounting)->toBeTrue();

    $adminUser = User::query()->where('email', 'admin.user@commu-core.app')->firstOrFail();
    $adminMember = Member::query()->where('user_id', $adminUser->id)->firstOrFail();
    expect($adminUser->name)->toBe($adminMember->first_name.' '.$adminMember->name);

    foreach (EventVisitor::query()->whereNotNull('email')->get() as $visitor) {
        expect($visitor->email)->toStartWith(Str::slug($visitor->name));
    }

    Storage::disk('public')->assertExists('branding/logos/demo.svg');
});
