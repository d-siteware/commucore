<?php

use App\Enums\MemberChangeField;
use App\Models\MemberChangeRequest;
use App\Models\Membership\Member;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('MemberChangeRequest model', function () {

    it('belongs to a member', function () {
        $request = MemberChangeRequest::factory()->create();

        expect($request->member)->toBeInstanceOf(Member::class);
    });

    it('belongs to a reviewing user', function () {
        $user = User::factory()->create();
        $request = MemberChangeRequest::factory()->create(['reviewed_by' => $user->id]);

        expect($request->reviewedBy)->toBeInstanceOf(User::class)
            ->and($request->reviewedBy->id)->toBe($user->id);
    });

    it('casts field to MemberChangeField enum', function () {
        $request = MemberChangeRequest::factory()->create([
            'field' => MemberChangeField::TYPE->value,
        ]);

        expect($request->field)->toBe(MemberChangeField::TYPE);
    });

    it('casts timestamps correctly', function () {
        $request = MemberChangeRequest::factory()->completed()->create();

        expect($request->completed_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and($request->reviewed_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    describe('isPending()', function () {
        it('returns true when neither completed nor rejected', function () {
            $request = MemberChangeRequest::factory()->pending()->create();

            expect($request->isPending())->toBeTrue();
        });

        it('returns false when completed', function () {
            $request = MemberChangeRequest::factory()->completed()->create();

            expect($request->isPending())->toBeFalse();
        });

        it('returns false when rejected', function () {
            $request = MemberChangeRequest::factory()->rejected()->create();

            expect($request->isPending())->toBeFalse();
        });
    });

    describe('statusLabel()', function () {
        it('returns pending label', function () {
            $request = MemberChangeRequest::factory()->pending()->create();

            expect($request->statusLabel())->toBe(__('change_request.status.pending'));
        });

        it('returns completed label', function () {
            $request = MemberChangeRequest::factory()->completed()->create();

            expect($request->statusLabel())->toBe(__('change_request.status.completed'));
        });

        it('returns rejected label', function () {
            $request = MemberChangeRequest::factory()->rejected()->create();

            expect($request->statusLabel())->toBe(__('change_request.status.rejected'));
        });
    });

    describe('statusColor()', function () {
        it('returns yellow when pending', function () {
            $request = MemberChangeRequest::factory()->pending()->create();

            expect($request->statusColor())->toBe('yellow');
        });

        it('returns lime when completed', function () {
            $request = MemberChangeRequest::factory()->completed()->create();

            expect($request->statusColor())->toBe('lime');
        });

        it('returns red when rejected', function () {
            $request = MemberChangeRequest::factory()->rejected()->create();

            expect($request->statusColor())->toBe('red');
        });
    });
});

describe('MemberChangeField enum', function () {

    it('has correct values', function () {
        expect(MemberChangeField::TYPE->value)->toBe('type')
            ->and(MemberChangeField::FEE_TYPE->value)->toBe('fee_type');
    });

    it('returns options array with string keys and values', function () {
        $options = MemberChangeField::options();

        expect($options)->toBeArray()
            ->toHaveCount(2)
            ->toHaveKeys(['type', 'fee_type']);
    });

    it('detects open request for field', function () {
        $request = MemberChangeRequest::factory()->pending()->create([
            'field' => MemberChangeField::TYPE->value,
        ]);

        expect(MemberChangeField::TYPE->hasOpenRequest($request->member_id))->toBeTrue();
    });

    it('returns false when no open request exists for field', function () {
        $request = MemberChangeRequest::factory()->completed()->create([
            'field' => MemberChangeField::TYPE->value,
        ]);

        expect(MemberChangeField::TYPE->hasOpenRequest($request->member_id))->toBeFalse();
    });

    it('does not count rejected request as open', function () {
        $request = MemberChangeRequest::factory()->rejected()->create([
            'field' => MemberChangeField::TYPE->value,
        ]);

        expect(MemberChangeField::TYPE->hasOpenRequest($request->member_id))->toBeFalse();
    });
});
