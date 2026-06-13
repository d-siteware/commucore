<?php

declare(strict_types=1);

use App\Models\Document;
use App\Models\User;
use App\Models\Funding\Funding;
use App\Enums\MemberDocumentCategory;

describe('Document model', function (): void {
    it('can be created with factory', function (): void {
        $funding = Funding::factory()->create();
        $document = Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => User::factory()->create()->id,
        ]);

        expect($document)->toBeInstanceOf(Document::class);
    });

    it('has a uuid on creation', function (): void {
        $funding = Funding::factory()->create();
        $document = Document::factory()->forModel($funding)->create([
            'uploaded_by_user_id' => User::factory()->create()->id,
        ]);

        expect($document->uuid)->not->toBeNull()
            ->and($document->uuid)->toBeString();
    });
});
