<?php

declare(strict_types=1);

use App\Models\Membership\MemberApplication;
use Carbon\Carbon;

describe('MemberApplication model', function (): void {
    it('can be created via createFromFormData', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'first_name' => 'John',
            'locale' => 'de',
            'country' => 'DE',
            'gender' => 'male',
            'is_deducted' => false,
        ]);

        expect($application)->toBeInstanceOf(MemberApplication::class)
            ->and($application->email)->toBe('test@example.com')
            ->and($application->name)->toBe('Doe')
            ->and($application->first_name)->toBe('John')
            ->and($application->locale)->toBe('de')
            ->and($application->country)->toBe('DE')
            ->and($application->token)->not->toBeNull()
            ->and($application->token)->toBeString()->toHaveLength(64)
            ->and($application->applied_at)->not->toBeNull()
            ->and($application->expires_at)->not->toBeNull();
    });

    it('casts birth_date to a Carbon date', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'first_name' => 'John',
            'locale' => 'de',
            'country' => 'DE',
            'birth_date' => '1990-05-15',
        ]);

        expect($application->birth_date)->toBeInstanceOf(Carbon::class)
            ->and($application->birth_date->format('Y-m-d'))->toBe('1990-05-15');
    });

    it('casts is_deducted as boolean', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
            'is_deducted' => 1,
        ]);

        expect($application->is_deducted)->toBeTrue();
    });

    it('returns pending when not accepted or rejected', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
        ]);

        expect($application->isPending())->toBeTrue()
            ->and($application->isAccepted())->toBeFalse()
            ->and($application->isRejected())->toBeFalse();
    });

    it('is accepted when accepted_at is set', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
            'accepted_at' => now(),
        ]);

        expect($application->isAccepted())->toBeTrue()
            ->and($application->isPending())->toBeFalse();
    });

    it('is rejected when rejected_at is set', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
            'rejected_at' => now(),
        ]);

        expect($application->isRejected())->toBeTrue()
            ->and($application->isPending())->toBeFalse();
    });

    it('is verified when verified_at is set', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
            'verified_at' => now(),
        ]);

        expect($application->isVerified())->toBeTrue();
    });

    it('is expired when expires_at is in the past', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
        ]);

        $application->expires_at = now()->subHour();
        $application->save();

        expect($application->isExpired())->toBeTrue();
    });

    it('is not expired when expires_at is in the future', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'test@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
        ]);

        expect($application->isExpired())->toBeFalse();
    });

    it('routes notifications to email', function (): void {
        $application = MemberApplication::createFromFormData([
            'email' => 'notify@example.com',
            'name' => 'Doe',
            'locale' => 'de',
            'country' => 'DE',
        ]);

        expect($application->routeNotificationForMail())->toBe('notify@example.com');
    });
});
