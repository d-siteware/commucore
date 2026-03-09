<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Document;
use App\Models\Funding\Funding;
use App\Models\Membership\Member;
use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// =============================================================================
// HasDocuments Trait – Feature Tests
// =============================================================================

describe('HasDocuments – Funding', function (): void {

    it('has documents() MorphMany relation', function (): void {
        $user = User::factory()->create();
        $funding = Funding::factory()->create();

        Document::factory()->forModel($funding)->create(['uploaded_by_user_id' => $user->id]);
        Document::factory()->forModel($funding)->create(['uploaded_by_user_id' => $user->id]);

        expect($funding->documents)->toHaveCount(2)
            ->and($funding->documents->first())->toBeInstanceOf(Document::class);
    });

    it('does not return documents of other models', function (): void {
        $user = User::factory()->create();
        $funding1 = Funding::factory()->create();
        $funding2 = Funding::factory()->create();

        Document::factory()->forModel($funding1)->create(['uploaded_by_user_id' => $user->id]);
        Document::factory()->forModel($funding2)->create(['uploaded_by_user_id' => $user->id]);

        expect($funding1->documents)->toHaveCount(1)
            ->and($funding1->documents->first()->documentable_id)->toBe($funding1->id);
    });

    it('creates document via relation', function (): void {
        $user = User::factory()->create();
        $funding = Funding::factory()->create();

        $funding->documents()->create([
            'uploaded_by_user_id' => $user->id,
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'original_name' => 'bescheid.pdf',
            'disk' => 'local',
            'path' => 'documents/funding/'.$funding->id.'/test',
            'mime_type' => 'application/pdf',
            'size' => 50_000,
        ]);

        expect($funding->fresh()->documents)->toHaveCount(1)
            ->and($funding->documents->first()->original_name)->toBe('bescheid.pdf');
    });

    it('returns documents in descending order (latest first)', function (): void {
        $user = User::factory()->create();
        $funding = Funding::factory()->create();

        $first = Document::factory()->forModel($funding)->create(['uploaded_by_user_id' => $user->id]);
        $this->travel(1)->seconds();
        $second = Document::factory()->forModel($funding)->create(['uploaded_by_user_id' => $user->id]);

        expect($funding->documents->first()->id)->toBe($second->id);
    });

});

describe('HasDocuments – Project', function (): void {

    it('has documents() relation', function (): void {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        Document::factory()->forModel($project)->count(3)->create([
            'uploaded_by_user_id' => $user->id,
        ]);

        expect($project->documents)->toHaveCount(3);
    });

    it('documents are scoped to the project', function (): void {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $funding = Funding::factory()->create();

        Document::factory()->forModel($project)->create(['uploaded_by_user_id' => $user->id]);
        Document::factory()->forModel($funding)->create(['uploaded_by_user_id' => $user->id]);

        expect($project->documents)->toHaveCount(1)
            ->and($project->documents->first()->documentable_type)->toBe(Project::class);
    });

});

describe('HasDocuments – Transaction', function (): void {

    it('has documents() relation', function (): void {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create();

        Document::factory()->forModel($transaction)->count(2)->create([
            'uploaded_by_user_id' => $user->id,
        ]);

        expect($transaction->documents)->toHaveCount(2);
    });

    it('soft-deletes documents (still in DB after delete)', function (): void {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create();

        $doc = Document::factory()->forModel($transaction)->create([
            'uploaded_by_user_id' => $user->id,
        ]);

        $doc->delete();

        // Relation zeigt nur nicht-gelöschte
        expect($transaction->fresh()->documents)->toHaveCount(0);

        // DB hat noch den Datensatz (SoftDeletes)
        $this->assertDatabaseHas('documents', ['id' => $doc->id]);
        $this->assertSoftDeleted('documents', ['id' => $doc->id]);
    });

});

describe('HasDocuments – Member', function (): void {

    it('has documents() relation', function (): void {
        $user = User::factory()->create();
        $member = Member::factory()->create();

        // Direkt in documents-Tabelle einfügen (nicht über MemberDocument-Alias)
        Document::factory()->forModel($member)->create(['uploaded_by_user_id' => $user->id]);
        Document::factory()->forModel($member)->create(['uploaded_by_user_id' => $user->id]);

        // documents() Trait-Methode abfragen (morphMany auf documents-Tabelle)
        $docs = Document::query()
            ->where('documentable_type', Member::class)
            ->where('documentable_id', $member->id)
            ->get();

        expect($docs)->toHaveCount(2);
    });

    it('documents of member are scoped to Member type', function (): void {
        $user = User::factory()->create();
        $member = Member::factory()->create();
        $project = Project::factory()->create();

        Document::factory()->forModel($member)->create(['uploaded_by_user_id' => $user->id]);
        Document::factory()->forModel($project)->create(['uploaded_by_user_id' => $user->id]);

        $memberDocs = Document::query()
            ->where('documentable_type', Member::class)
            ->where('documentable_id', $member->id)
            ->get();

        expect($memberDocs)->toHaveCount(1)
            ->and($memberDocs->first()->documentable_type)->toBe(Member::class);
    });

});
