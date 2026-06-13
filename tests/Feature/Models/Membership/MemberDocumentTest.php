<?php

declare(strict_types=1);

use App\Enums\MemberDocumentCategory;
use App\Models\Document;
use App\Models\Membership\Member;
use App\Models\Membership\MemberDocument;
use App\Models\User;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;

describe('MemberDocument model', function (): void {
    it('extends Document', function (): void {
        $doc = new MemberDocument;

        expect($doc)->toBeInstanceOf(Document::class);
    });

    it('uses the documents table', function (): void {
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $doc = new MemberDocument;
        $doc->uuid = Str::uuid()->toString();
        $doc->member_id = $member->id;
        $doc->uploaded_by_user_id = $user->id;
        $doc->original_name = 'test.pdf';
        $doc->path = 'documents/test/1/test.pdf';
        $doc->mime_type = 'application/pdf';
        $doc->size = 1024;
        $doc->save();

        expect($doc->getTable())->toBe('documents');
        assertDatabaseHas('documents', [
            'id' => $doc->id,
            'documentable_type' => Member::class,
            'documentable_id' => $member->id,
        ]);
    });

    it('automatically sets documentable_type to Member on create', function (): void {
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $doc = new MemberDocument;
        $doc->uuid = Str::uuid()->toString();
        $doc->member_id = $member->id;
        $doc->uploaded_by_user_id = $user->id;
        $doc->original_name = 'test.pdf';
        $doc->path = 'documents/test/1/test.pdf';
        $doc->mime_type = 'application/pdf';
        $doc->size = 1024;
        $doc->save();

        expect($doc->documentable_type)->toBe(Member::class);
    });

    it('belongs to a member', function (): void {
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $doc = new MemberDocument;
        $doc->uuid = Str::uuid()->toString();
        $doc->member_id = $member->id;
        $doc->uploaded_by_user_id = $user->id;
        $doc->original_name = 'test.pdf';
        $doc->path = 'documents/test/1/test.pdf';
        $doc->mime_type = 'application/pdf';
        $doc->size = 1024;
        $doc->save();

        expect($doc->member)->toBeInstanceOf(Member::class)
            ->and($doc->member->id)->toBe($member->id);
    });

    it('has member_id as proxy for documentable_id', function (): void {
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $doc = new MemberDocument;
        $doc->uuid = Str::uuid()->toString();
        $doc->member_id = $member->id;
        $doc->uploaded_by_user_id = $user->id;
        $doc->original_name = 'test.pdf';
        $doc->path = 'documents/test/1/test.pdf';
        $doc->mime_type = 'application/pdf';
        $doc->size = 1024;
        $doc->save();

        expect($doc->member_id)->toBe($member->id)
            ->and($doc->documentable_id)->toBe($member->id);
    });

    it('casts category to MemberDocumentCategory enum', function (): void {
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $doc = new MemberDocument;
        $doc->uuid = Str::uuid()->toString();
        $doc->member_id = $member->id;
        $doc->uploaded_by_user_id = $user->id;
        $doc->original_name = 'test.pdf';
        $doc->path = 'documents/test/1/test.pdf';
        $doc->mime_type = 'application/pdf';
        $doc->size = 1024;
        $doc->category = MemberDocumentCategory::Sepa;
        $doc->save();

        expect($doc->category)->toBeInstanceOf(MemberDocumentCategory::class)
            ->and($doc->category)->toBe(MemberDocumentCategory::Sepa);
    });

    it('only returns documents scoped to Member type', function (): void {
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $md = new MemberDocument;
        $md->uuid = Str::uuid()->toString();
        $md->member_id = $member->id;
        $md->uploaded_by_user_id = $user->id;
        $md->original_name = 'member.pdf';
        $md->path = 'documents/test/1/member.pdf';
        $md->mime_type = 'application/pdf';
        $md->size = 1024;
        $md->save();

        Document::factory()->create([
            'documentable_type' => 'App\Models\SomeOtherModel',
            'documentable_id' => 1,
        ]);

        $memberDocs = MemberDocument::all();
        expect($memberDocs)->toHaveCount(1)
            ->and($memberDocs->first()->id)->toBe($md->id);
    });

    it('can scope by member id', function (): void {
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();
        $user = User::factory()->create();

        $doc = new MemberDocument;
        $doc->uuid = Str::uuid()->toString();
        $doc->member_id = $member1->id;
        $doc->uploaded_by_user_id = $user->id;
        $doc->original_name = 'doc1.pdf';
        $doc->path = 'documents/test/1/doc1.pdf';
        $doc->mime_type = 'application/pdf';
        $doc->size = 1024;
        $doc->save();

        expect(MemberDocument::whereMemberId($member1->id)->count())->toBe(1)
            ->and(MemberDocument::whereMemberId($member2->id)->count())->toBe(0);
    });

    it('can scope by category', function (): void {
        $member = Member::factory()->create();
        $user = User::factory()->create();

        $doc = new MemberDocument;
        $doc->uuid = Str::uuid()->toString();
        $doc->member_id = $member->id;
        $doc->uploaded_by_user_id = $user->id;
        $doc->original_name = 'sepa.pdf';
        $doc->path = 'documents/test/1/sepa.pdf';
        $doc->mime_type = 'application/pdf';
        $doc->size = 1024;
        $doc->category = MemberDocumentCategory::Sepa;
        $doc->save();

        $results = MemberDocument::ofCategory(MemberDocumentCategory::Sepa)->get();
        expect($results)->toHaveCount(1);

        $other = MemberDocument::ofCategory(MemberDocumentCategory::MembershipForm)->get();
        expect($other)->toHaveCount(0);
    });
});
