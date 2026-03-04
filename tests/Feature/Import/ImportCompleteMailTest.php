<?php

declare(strict_types=1);

use App\Mail\MemberImportCompleted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('sends import completed mail with correct data', function (): void {
    Mail::fake();

    $user = User::factory()->create(['name' => 'Admin']);

    $protocol = [
        'imported' => 10,
        'skipped' => 2,
        'errors' => [['row' => 5, 'reason' => 'Pflichtfeld fehlt']],
        'duration_ms' => 320,
    ];

    Mail::to($user->email)->send(new MemberImportCompleted(
        user: $user,
        protocol: $protocol,
        backupDownloadUrl: 'https://example.com/backup',
        importedAt: now()->toDateTimeString(),
    ));

    Mail::assertSent(MemberImportCompleted::class, function (MemberImportCompleted $mail) use ($user): bool {
        return $mail->user->id === $user->id
            && $mail->protocol['imported'] === 10
            && $mail->protocol['skipped'] === 2;
    });
});

it('mail has correct subject', function (): void {
    $user = User::factory()->create();

    $mail = new MemberImportCompleted(
        user: $user,
        protocol: ['imported' => 0, 'skipped' => 0, 'errors' => [], 'duration_ms' => 0],
        backupDownloadUrl: 'https://example.com/backup',
        importedAt: now()->toDateTimeString(),
    );

    expect($mail->envelope()->subject)->toBe(__('members.import.mail.subject'));
});
