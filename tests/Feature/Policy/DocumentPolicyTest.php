<?php

declare(strict_types=1);

use App\Models\Document;
use App\Models\Funding\Funding;
use App\Models\Membership\Member;
use App\Models\Project\Project;
use App\Models\User;
use App\Policies\DocumentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// =============================================================================
// DocumentPolicy – Unit Tests
// =============================================================================
// Die Policy delegiert an die Policy des Eltern-Models.
// Funding/Project/Member nutzen is_admin als Schreibrecht.
// =============================================================================

function makeDocument(\Illuminate\Database\Eloquent\Model $parent, User $uploader): Document
{
    $doc = Document::factory()->forModel($parent)->create([
        'uploaded_by_user_id' => $uploader->id,
    ]);
    $doc->setRelation('documentable', $parent);

    return $doc;
}

describe('DocumentPolicy – viewAny()', function (): void {

    it('allows any authenticated user', function (): void {
        $user = User::factory()->create();
        $policy = new DocumentPolicy;

        expect($policy->viewAny($user))->toBeTrue();
    });

});

describe('DocumentPolicy – view()', function (): void {

    it('allows view when user can view the parent Funding', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $user);

        expect($user->can('view', $doc))->toBeTrue();
    });

    it('allows view when user can view the parent Project', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $project = Project::factory()->create();
        $doc = makeDocument($project, $user);

        expect($user->can('view', $doc))->toBeTrue();
    });

    it('allows view when user can view the parent Member', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $member = Member::factory()->create();
        $doc = makeDocument($member, $user);

        expect($user->can('view', $doc))->toBeTrue();
    });

    it('denies view when documentable relation is not loaded', function (): void {
        $user = User::factory()->create();
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $user);
        // Relation NICHT gesetzt – getRelation() gibt null zurück
        $doc->unsetRelation('documentable');

        $policy = new DocumentPolicy;
        expect($policy->view($user, $doc))->toBeFalse();
    });

});

describe('DocumentPolicy – create()', function (): void {

    it('allows create when user can update the parent Funding', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $user);

        $policy = new DocumentPolicy;
        expect($policy->create($user, $doc))->toBeTrue();
    });

    it('denies create for non-admin user', function (): void {
        $admin = User::factory()->create(['is_admin' => true]);
        $noAdmin = User::factory()->create(['is_admin' => false]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $admin);

        $policy = new DocumentPolicy;
        expect($policy->create($noAdmin, $doc))->toBeFalse();
    });

    it('denies create when documentable is not loaded', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $user);
        $doc->unsetRelation('documentable');

        $policy = new DocumentPolicy;
        expect($policy->create($user, $doc))->toBeFalse();
    });

});

describe('DocumentPolicy – delete()', function (): void {

    it('allows uploader to always delete their own document', function (): void {
        $uploader = User::factory()->create(['is_admin' => false]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $uploader);

        $policy = new DocumentPolicy;
        expect($policy->delete($uploader, $doc))->toBeTrue();
    });

    it('allows admin to delete any document', function (): void {
        $uploader = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $uploader);

        $policy = new DocumentPolicy;
        expect($policy->delete($admin, $doc))->toBeTrue();
    });

    it('denies non-admin non-uploader from deleting', function (): void {
        $uploader = User::factory()->create();
        $other = User::factory()->create(['is_admin' => false]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $uploader);

        $policy = new DocumentPolicy;
        expect($policy->delete($other, $doc))->toBeFalse();
    });

    it('denies delete when documentable is not loaded and user is not uploader', function (): void {
        $uploader = User::factory()->create();
        $other = User::factory()->create();
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $uploader);
        $doc->unsetRelation('documentable');

        $policy = new DocumentPolicy;
        expect($policy->delete($other, $doc))->toBeFalse();
    });

});

describe('DocumentPolicy – update/restore/forceDelete()', function (): void {

    it('always denies update', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $user);

        $policy = new DocumentPolicy;
        expect($policy->update($user, $doc))->toBeFalse();
    });

    it('always denies restore', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $user);

        $policy = new DocumentPolicy;
        expect($policy->restore($user, $doc))->toBeFalse();
    });

    it('always denies forceDelete', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $funding = Funding::factory()->create();
        $doc = makeDocument($funding, $user);

        $policy = new DocumentPolicy;
        expect($policy->forceDelete($user, $doc))->toBeFalse();
    });

});
