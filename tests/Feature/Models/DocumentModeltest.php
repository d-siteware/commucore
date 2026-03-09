<?php

declare(strict_types=1);

use App\Models\Document;
use App\Models\Funding\Funding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// =============================================================================
// Document Model – Unit Tests
// =============================================================================

describe('Document Model – fileSizeForHumans()', function (): void {

    it('formats bytes correctly', function (): void {
        $doc = new Document(['size' => 512]);
        expect($doc->fileSizeForHumans())->toBe('512 B');
    });

    it('formats kilobytes correctly', function (): void {
        $doc = new Document(['size' => 2_048]);
        expect($doc->fileSizeForHumans())->toBe('2.00 KB');
    });

    it('formats megabytes correctly', function (): void {
        $doc = new Document(['size' => 2_097_152]);
        expect($doc->fileSizeForHumans())->toBe('2.00 MB');
    });

    it('formats exactly 1 KB boundary', function (): void {
        $doc = new Document(['size' => 1_024]);
        expect($doc->fileSizeForHumans())->toBe('1.00 KB');
    });

    it('formats exactly 1 MB boundary', function (): void {
        $doc = new Document(['size' => 1_048_576]);
        expect($doc->fileSizeForHumans())->toBe('1.00 MB');
    });

});

describe('Document Model – icon()', function (): void {

    it('returns document-text for PDF', function (): void {
        $doc = new Document(['mime_type' => 'application/pdf', 'original_name' => 'file.pdf']);
        expect($doc->icon())->toBe('document-text');
    });

    it('returns photo for JPEG image', function (): void {
        $doc = new Document(['mime_type' => 'image/jpeg', 'original_name' => 'photo.jpg']);
        expect($doc->icon())->toBe('photo');
    });

    it('returns photo for PNG image', function (): void {
        $doc = new Document(['mime_type' => 'image/png', 'original_name' => 'photo.png']);
        expect($doc->icon())->toBe('photo');
    });

    it('returns document for Word file', function (): void {
        $doc = new Document([
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'original_name' => 'brief.docx',
        ]);
        expect($doc->icon())->toBe('document');
    });

    it('returns table-cells for Excel file', function (): void {
        $doc = new Document([
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'original_name' => 'tabelle.xlsx',
        ]);
        expect($doc->icon())->toBe('table-cells');
    });

    it('returns envelope for eml file by MIME type', function (): void {
        $doc = new Document(['mime_type' => 'message/rfc822', 'original_name' => 'mail.eml']);
        expect($doc->icon())->toBe('envelope');
    });

    it('returns envelope for eml file by extension fallback', function (): void {
        $doc = new Document(['mime_type' => 'application/octet-stream', 'original_name' => 'mail.eml']);
        expect($doc->icon())->toBe('envelope');
    });

    it('returns paper-clip for unknown type', function (): void {
        $doc = new Document(['mime_type' => 'application/zip', 'original_name' => 'archive.zip']);
        expect($doc->icon())->toBe('paper-clip');
    });

});

describe('Document Model – recordAccess()', function (): void {

    it('sets last_accessed_at and last_accessed_by_user_id', function (): void {
        $user = User::factory()->create();
        $funding = Funding::factory()->create();

        $document = Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => $user->id,
        ]);

        expect($document->last_accessed_at)->toBeNull()
            ->and($document->last_accessed_by_user_id)->toBeNull();

        $document->recordAccess($user);
        $document->refresh();

        expect($document->last_accessed_at)->not->toBeNull()
            ->and($document->last_accessed_by_user_id)->toBe($user->id);
    });

    it('updates last_accessed_at on repeated access', function (): void {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $funding = Funding::factory()->create();

        $document = Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => $user->id,
        ]);

        $document->recordAccess($user);
        $document->refresh();
        $firstAccess = $document->last_accessed_at;

        // Zeit vorrücken damit Timestamps unterschiedlich sind
        $this->travel(2)->seconds();

        $document->recordAccess($user2);
        $document->refresh();

        expect($document->last_accessed_by_user_id)->toBe($user2->id)
            ->and($document->last_accessed_at->gt($firstAccess))->toBeTrue();
    });

});

describe('Document Model – relationships', function (): void {

    it('belongs to uploadedBy user', function (): void {
        $user = User::factory()->create();
        $funding = Funding::factory()->create();
        $document = Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => $user->id,
        ]);

        expect($document->uploadedBy->id)->toBe($user->id);
    });

    it('belongs to lastAccessedBy user', function (): void {
        $uploader = User::factory()->create();
        $accessor = User::factory()->create();
        $funding = Funding::factory()->create();

        $document = Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => $uploader->id,
            'last_accessed_by_user_id' => $accessor->id,
        ]);

        expect($document->lastAccessedBy->id)->toBe($accessor->id);
    });

    it('morphs to its documentable', function (): void {
        $funding = Funding::factory()->create();
        $document = Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => User::factory()->create()->id,
        ]);

        expect($document->documentable)->toBeInstanceOf(Funding::class)
            ->and($document->documentable->id)->toBe($funding->id);
    });

    it('scopeNeverAccessed returns only unaccessed documents', function (): void {
        $user = User::factory()->create();
        $funding = Funding::factory()->create();

        Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => $user->id,
            'last_accessed_at' => null,
        ]);
        Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => $user->id,
            'last_accessed_at' => now(),
        ]);

        expect(Document::neverAccessed()->count())->toBe(1);
    });

});
