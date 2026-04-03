<?php

use App\Models\MemberCancellationRequest;
use App\Models\Membership\Member;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('MemberCancellationRequest model', function () {

    it('belongs to a member', function () {
        $request = MemberCancellationRequest::factory()->create();

        expect($request->member)->toBeInstanceOf(Member::class);
    });

    it('belongs to a reviewing user', function () {
        $user = User::factory()->create();
        $request = MemberCancellationRequest::factory()->create(['reviewed_by' => $user->id]);

        expect($request->reviewedBy)->toBeInstanceOf(User::class)
            ->and($request->reviewedBy->id)->toBe($user->id);
    });

    it('casts requested_leave_date to Carbon date', function () {
        $request = MemberCancellationRequest::factory()->withLeaveDate()->create();

        expect($request->requested_leave_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('allows null requested_leave_date', function () {
        $request = MemberCancellationRequest::factory()->create([
            'requested_leave_date' => null,
        ]);

        expect($request->requested_leave_date)->toBeNull();
    });

    describe('isPending()', function () {
        it('returns true when neither confirmed nor rejected', function () {
            $request = MemberCancellationRequest::factory()->pending()->create();

            expect($request->isPending())->toBeTrue();
        });

        it('returns false when confirmed', function () {
            $request = MemberCancellationRequest::factory()->confirmed()->create();

            expect($request->isPending())->toBeFalse();
        });

        it('returns false when rejected', function () {
            $request = MemberCancellationRequest::factory()->rejected()->create();

            expect($request->isPending())->toBeFalse();
        });
    });

    describe('statusLabel()', function () {
        it('returns pending label', function () {
            $request = MemberCancellationRequest::factory()->pending()->create();

            expect($request->statusLabel())->toBe(__('cancellation_request.status.pending'));
        });

        it('returns confirmed label', function () {
            $request = MemberCancellationRequest::factory()->confirmed()->create();

            expect($request->statusLabel())->toBe(__('cancellation_request.status.confirmed'));
        });

        it('returns rejected label', function () {
            $request = MemberCancellationRequest::factory()->rejected()->create();

            expect($request->statusLabel())->toBe(__('cancellation_request.status.rejected'));
        });
    });

    describe('statusColor()', function () {
        it('returns yellow when pending', function () {
            $request = MemberCancellationRequest::factory()->pending()->create();

            expect($request->statusColor())->toBe('yellow');
        });

        it('returns lime when confirmed', function () {
            $request = MemberCancellationRequest::factory()->confirmed()->create();

            expect($request->statusColor())->toBe('lime');
        });

        it('returns red when rejected', function () {
            $request = MemberCancellationRequest::factory()->rejected()->create();

            expect($request->statusColor())->toBe('red');
        });
    });
});
