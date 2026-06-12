<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\Protocols\Minutes\ActionItem;
use App\Models\Protocols\Minutes\Attendee;
use App\Models\Protocols\Minutes\MeetingMinute;
use App\Models\Protocols\Minutes\MeetingTopic;
use App\Models\User;
use App\Services\PdfGeneratorService;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

test('index page renders for admin users', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $this->actingAs($admin);

    Livewire::test('app.tool.meetingminutes.index')
        ->assertStatus(200)
        ->assertSee(__('minutes.index.page_title'))
        ->assertSeeInOrder(['flux-heading', 'Protokolle']);
});

test('index page renders for accountant users', function (): void {
    Config::set('app.accountant_email', 'accountant@example.com');
    $accountant = User::factory()->accountant()->create(['email_verified_at' => now()]);
    $this->actingAs($accountant);

    Livewire::test('app.tool.meetingminutes.index')
        ->assertStatus(200)
        ->assertSee(__('minutes.index.page_title'));
});

test('index page renders for board member users', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $member = Member::factory()->boardMember()->withUser()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    Livewire::test('app.tool.meetingminutes.index')
        ->assertStatus(200)
        ->assertSee(__('minutes.index.page_title'));
});

test('regular users can view index if permitted to view specific minutes', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($user);

    Livewire::test('app.tool.meetingminutes.index')
        ->assertStatus(200)
        ->assertSee(__('minutes.index.page_title'));
});

test('unauthenticated users are redirected from index page', function (): void {
    $this->get('/backend/minutes')
        ->assertRedirect('/login');
});

test('index page displays meeting minutes with attendees', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $member = Member::factory()->create();
    $minute = MeetingMinute::factory()
        ->has(Attendee::factory()->state(['member_id' => $member->id]), 'attendees')
        ->has(MeetingTopic::factory()->count(1), 'topics')
        ->create(['title' => 'Team Sync', 'meeting_date' => '2025-05-20']);

    $this->actingAs($admin);
    Livewire::test('app.tool.meetingminutes.index')
        ->assertSee($minute->title)
//        ->assertSee($minute->attendees->first()->name)
        ->assertSee($minute->meeting_date->isoformat('LL'));
});

test('search filters meeting minutes by title and topic content', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $member = Member::factory()->create();
    $minute1 = MeetingMinute::factory()
        ->has(Attendee::factory()->state(['member_id' => $member->id]))
        ->has(MeetingTopic::factory()->state(['content' => 'Project Update']), 'topics')
        ->create(['title' => 'Team Sync']);
    $minute2 = MeetingMinute::factory()
        ->has(Attendee::factory()->state(['member_id' => $member->id]))
        ->create(['title' => 'Budget Review']);

    $this->actingAs($admin);

    Livewire::test('app.tool.meetingminutes.index')
        ->set('search', 'Project')
        ->assertSee($minute1->title)
        ->assertDontSee($minute2->title);
});

test('sorting works for title and meeting_date', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $member = Member::factory()->create();
    $minute1 = MeetingMinute::factory()
        ->has(Attendee::factory()->state(['member_id' => $member->id]))
        ->has(MeetingTopic::factory()->count(1), 'topics')
        ->create(['title' => 'Alpha Meeting', 'meeting_date' => '2025-05-20']);
    $minute2 = MeetingMinute::factory()->has(Attendee::factory()->state(['member_id' => $member->id]))
        ->has(MeetingTopic::factory()->count(1), 'topics')
        ->create(['title' => 'Zeta Meeting', 'meeting_date' => '2025-05-21']);

    $this->actingAs($admin);

    Livewire::test('app.tool.meetingminutes.index')
        ->call('sort', 'title')
        ->assertSeeInOrder([$minute1->title, $minute2->title])
        ->call('sort', 'title')
        ->assertSeeInOrder([$minute2->title, $minute1->title]);
});

test('pagination works for meeting minutes', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $member = Member::factory()->create();
    MeetingMinute::factory()->count(15)
        ->has(MeetingTopic::factory()->count(1), 'topics')
        ->has(Attendee::factory()->state(['member_id' => $member->id]))->create();

    $this->actingAs($admin);

    Livewire::test('app.tool.meetingminutes.index')
        ->assertSee('flux-table')
        ->assertSee(MeetingMinute::first()->title)
        ->call('gotoPage', 2)
        ->assertDontSee(MeetingMinute::first()->title);
});

test('create button is visible only to admin, accountant, or board member users', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $this->actingAs($admin);

    $component = Livewire::test('app.tool.meetingminutes.index');

    $component->assertSee(__('minutes.index.btn.create'));

    Config::set('app.accountant_email', 'accountant@example.com');
    $accountant = User::factory()->accountant()->create(['email_verified_at' => now()]);
    $this->actingAs($accountant);

    $component->assertSee(__('minutes.index.btn.create'));

    $boardMember = User::factory()->create(['email_verified_at' => now()]);
    $member = Member::factory()->boardMember()->withUser()->create(['user_id' => $boardMember->id]);
    $this->actingAs($boardMember);

    Livewire::test('app.tool.meetingminutes.index')
        ->assertSee(__('minutes.index.btn.create'));

    $regular = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($regular);

    Livewire::test('app.tool.meetingminutes.index')
        ->assertDontSee(__('minutes.index.btn.create'));
});

test('fetching meeting minutes details works for any authenticated user', function (): void {
    $member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);
    $minute = MeetingMinute::factory()
        ->has(Attendee::factory()->state(['member_id' => $member->id]))
        ->has(MeetingTopic::factory()->count(1), 'topics')
        ->has(ActionItem::factory()->state(['assignee_id' => $member->id]))
        ->create(['title' => 'Team Sync']);

    $this->actingAs($member->user);

    Livewire::test('app.tool.meetingminutes.index')
        ->call('fetchMeetingMinutes', $minute->id)
        ->assertSet('selectedMeeting.id', $minute->id)
        ->assertSee($minute->attendees->first()->name)
        ->assertSee($minute->topics->first()->content);
});

test('edit meeting minutes redirects for admin, accountant, or board member users', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $this->actingAs($admin);

    MeetingMinute::factory()->count(15)->create();

    Livewire::test('app.tool.meeting-minutes.edit')
        ->assertStatus(200)
        ->assertSee(__('minutes.edit.page_title'))
        ->assertSeeInOrder(['flux-heading', 'Protokoll bearbeiten']);
});

// test('regular users cannot edit meeting minutes', function () {
//     $user = User::factory()->create(['email_verified_at' => now()]);
//     $member = Member::factory()->create();
//     $minute = MeetingMinute::factory()->has(Attendee::factory()->state(['member_id' => $member->id]))->create();

//     $this->actingAs($user);

//     Livewire::test('app.tool.meetingminutes.index')
//         ->call('editMeetingMinutes', $minute->id)
//         ->assertForbidden();
// });

//
//  TODO Fix test later
//
// test('print meeting minutes generates PDF for any authenticated user', function () {
//    $user = User::factory()->create(['email_verified_at' => now()]);
//    $member = Member::factory()->create();
//    $minute = MeetingMinute::factory()
//        ->has(Attendee::factory()->state(['member_id' => $member->id]))
//        ->has(MeetingTopic::factory()->count(1), 'topics')
//        ->create();
//    $pdfContent = '%PDF-1.4 test content';
//
//    // Mock static method
//    $mock = Mockery::mock('alias:App\Services\PdfGeneratorService');
//    $mock->shouldReceive('generatePdf')
//        ->with('meeting-minute', $minute, null, true, null)
//        ->andReturn($pdfContent);
//    // Bind mock to container
//    app()->bind('App\Services\PdfGeneratorService', fn () => $mock);
//    // Ensure static calls are intercepted
// //    Mockery::getContainer()->mock('alias:App\Services\PdfGeneratorService', $mock);
//
//    $this->actingAs($user);
//
//    // Bypass authorization
//    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authorize::class);
//
//    $filename = "meeting-minute-{$minute->id}-".now()->format('Ymd').'.pdf';
//
//    $response = Livewire::test('app.tool.meetingminutes.index')
//        ->call('printMeetingMinutes', $minute->id);
//
//    $response->assertStatus(200);
//
//    // Verify download effect
//    $effects = $response->effects;
//    $this->assertArrayHasKey('download', $effects, 'Download effect not present in response');
//    $this->assertEquals($filename, $effects['download']['name'], 'Download filename does not match');
//    $actualContent = isset($effects['download']['content']) ? base64_decode($effects['download']['content'], true) ?: $effects['download']['content'] : null;
//    $this->assertEquals($pdfContent, $actualContent, 'Download content does not match');
// });

test('print meeting minutes handles PDF generation failure', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $member = Member::factory()->create();
    $minute = MeetingMinute::factory()
        ->has(Attendee::factory()->state(['member_id' => $member->id]))
        ->has(MeetingTopic::factory()->count(1), 'topics')
        ->create();

    // Mock the instance method via container (avoids alias leak into other tests)
    $mock = Mockery::mock(PdfGeneratorService::class);
    $mock->shouldReceive('generate')
        ->with('meeting-minute', $minute, null, true, null)
        ->andThrow(new Exception('PDF generation failed'));
    app()->instance(PdfGeneratorService::class, $mock);

    $this->actingAs($user);

    $this->withoutMiddleware(Authorize::class);

    Livewire::test('app.tool.meetingminutes.index')
        ->call('printMeetingMinutes', $minute->id)
        ->assertSessionHas('error', 'Fehler beim Erstellen des Protokolls.');
});

test('debug meeting minutes query', function (): void {
    $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
    $member = Member::factory()->create();
    MeetingMinute::factory()->count(5)
        ->has(Attendee::factory()->state(['member_id' => $member->id]))
        ->has(MeetingTopic::factory()->count(1), 'topics')
        ->create();

    $this->actingAs($admin);

    // Verify records exist in the database
    expect(MeetingMinute::count())->toBe(5);
    expect(Attendee::where('member_id', $member->id)->count())->toBe(5);

    $component = Livewire::test('app.tool.meetingminutes.index');
    $minutes = $component->instance()->minutes; // Access as property, not method

    // Debug the paginator
    $minutesArray = $minutes->items();
    $this->assertNotEmpty($minutesArray, 'Meeting minutes paginator is empty');
    expect($minutes->total())->toBe(5, 'Expected 5 meeting minutes, got '.$minutes->total());
});
