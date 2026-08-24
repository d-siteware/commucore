<?php

declare(strict_types=1);

use App\Actions\Jetstream\DeleteUser;
use App\Models\Accounting\AccountReport;
use App\Models\Blog\Post;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('user mit verknüpften Records kann gelöscht werden — Audit-Rows überleben mit null-Referenz', function (): void {
    $user = User::factory()->create();
    $member = Member::factory()->create();

    $post = Post::factory()->create(['user_id' => $user->id]);
    $report = AccountReport::factory()->create(['created_by' => $user->id]);

    DB::table('datev_exports')->insert([
        'account_report_id' => $report->id,
        'exported_by' => $user->id,
        'filename' => 'datev.zip',
        'exported_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('member_documents')->insert([
        'member_id' => $member->id,
        'uploaded_by_user_id' => $user->id,
        'uuid' => (string) str()->uuid(),
        'original_name' => 'antrag.pdf',
        'disk' => 'private',
        'path' => 'member-documents/test.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1234,
        'category' => 'antrag',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('mail_history_entries')->insert([
        'user_id' => $user->id,
        'subject' => json_encode(['de' => 'Test']),
        'message' => json_encode(['de' => 'Test']),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    app(DeleteUser::class)->delete($user);

    expect($user->fresh())->toBeNull();
    expect($post->fresh()->user_id)->toBeNull();
    expect($report->fresh()->created_by)->toBeNull();
    expect(DB::table('datev_exports')->first()->exported_by)->toBeNull();
    expect(DB::table('member_documents')->first()->uploaded_by_user_id)->toBeNull();
    expect(DB::table('mail_history_entries')->first()->user_id)->toBeNull();
});
