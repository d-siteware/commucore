<?php

declare(strict_types=1);

/**
* This feature has been deactivated
 *
* beforeEach(function () {
 * // Run migrations
 * $this->artisan('migrate', ['--database' => 'sqlite']);
 *
 * // Set up a test user (if needed for authentication)
 * $this->user = \App\Models\User::factory()->create();
 * actingAs($this->user);
 *
 * // Set application locale to 'de' (mimicking app()->getLocale())
 * //    App::setLocale('de');
 *
 * // Apply middleware to set locale
    * $this->withMiddleware(\App\Http\Middleware\SetLocale::class);
 *
* // Define test route for events.show
    * Route::get('/events/{slug}', fn ($slug) => 'Event details')->name('events.show');
* });
 *
* it('renders the calendar component successfully', function () {
    * Livewire::test(EventCalendar::class)
        * ->assertStatus(200)
        * ->assertSeeHtml('<h1 class="text-base font-semibold text-gray-900">')
        * ->assertSee(Carbon::now()->isoFormat('MMMM YYYY')); // e.g., "Juni 2025"
* });
 *
* it('displays translated day names', function () {
    * Livewire::test(EventCalendar::class)
        * ->assertSee(__('app.cal.day_short.Mo')) // Short form for mobile
        * ->assertSee(__('app.cal.day_medium.Mo')) // Medium form for desktop
        * ->assertSee(__('app.cal.day_short.Su'))
        * ->assertSee(__('app.cal.day_medium.Su'));
* });
 *
* it('displays events for the selected month', function () {
    * Event::create([
        * 'event_date' => Carbon::now(),
        * 'start_time' => '10:00:00',
        * 'title' => ['de' => 'Teamtreffen', 'hu' => 'Csapatértekezlet'],
        * 'excerpt' => ['de' => 'Monatliches Treffen.', 'hu' => 'Havi értekezlet.'],
        * 'slug' => ['de' => 'teamtreffen', 'hu' => 'csapatertekezlet'],
        * 'status' => EventStatus::PUBLISHED->value,
    * ]);
    * Event::create([
        * 'event_date' => Carbon::now()->addDays(5),
        * 'start_time' => '14:00:00',
        * 'title' => ['de' => 'Projektfrist', 'hu' => 'Projekt határideje'],
        * 'excerpt' => ['de' => 'Projekt abgeben.', 'hu' => 'Projekt leadása.'],
        * 'slug' => ['de' => 'projektfrist', 'hu' => 'projekt-hatarideje'],
        * 'status' => EventStatus::PUBLISHED->value,
    * ]);
 *
* Livewire::test(EventCalendar::class)
        * ->assertSee('Teamtreffen')
        * ->assertSee('Projektfrist')
        * ->assertSee('Monatliches Treffen.')
        * ->assertSee('Projekt abgeben.')
        * ->assertSee(Carbon::now()->isoFormat('Do MMMM')) // e.g., "12. Juni"
        * ->assertSee(Carbon::now()->addDays(5)->isoFormat('Do MMMM'))
        * ->assertSee('10:00') // 24-hour format
        * ->assertSee('14:00')
        * ->assertSeeHtml('href="'.route('events.show', 'teamtreffen').'"')
        * ->assertSeeHtml('href="'.route('events.show', 'projektfrist').'"');
* });
 *
* it('does not display unpublished events', function () {
    * Event::create([
        * 'event_date' => Carbon::now(),
        * 'title' => ['de' => 'Draft Event', 'hu' => 'Piszkozat Esemény'],
        * 'slug' => ['de' => 'draft-event', 'hu' => 'piszkozat-esemeny'],
        * 'status' => EventStatus::DRAFT->value,
    * ]);
 *
* Livewire::test(EventCalendar::class)
        * ->assertDontSee('Draft Event')
        * ->assertDontSeeHtml('href="'.route('events.show', 'draft-event').'"');
* });
 *
* it('navigates to previous month', function () {
    * $previousMonth = Carbon::now()->subMonth()->isoFormat('MMMM YYYY');
 *
* Livewire::test(EventCalendar::class)
        * ->call('previousMonth')
        * ->assertSee($previousMonth);
* });
 *
* it('navigates to next month', function () {
    * $nextMonth = Carbon::now()->addMonth()->isoFormat('MMMM YYYY');
 *
* Livewire::test(EventCalendar::class)
        * ->call('nextMonth')
        * ->assertSee($nextMonth);
* });
 *
* it('navigates to current month with goToToday', function () {
    * $currentMonth = Carbon::now()->isoFormat('MMMM YYYY');
 *
* Livewire::test(EventCalendar::class)
        * ->set('selectedMonth', Carbon::now()->subMonth()->month)
        * ->set('selectedYear', Carbon::now()->subMonth()->year)
        * ->call('goToToday')
        * ->assertSee($currentMonth)
        * ->assertSee(__('app.today')); // Translated "Today" button
* });
 *
* it('displays no events message when no events exist', function () {
    * Livewire::test(EventCalendar::class)
        * ->assertSee(__('app.cal.empty')); // Translated empty state
* });
 *
* it('handles missing translations gracefully', function () {
    * Event::create([
        * 'event_date' => Carbon::now(),
        * 'title' => ['hu' => 'Csapatértekezlet'],
        * 'excerpt' => ['hu' => 'Havi értekezlet.'],
        * 'slug' => ['hu' => 'csapatertekezlet'],
        * 'status' => EventStatus::PUBLISHED->value,
    * ]);
 *
* Livewire::test(EventCalendar::class)
        * ->assertSee('Csapatértekezlet')
        * ->assertSee('Havi értekezlet.')
        * ->assertSeeHtml('href="'.route('events.show', 'csapatertekezlet').'"');
* });
 *
* it('uses slug fallback when no locale translation exists', function () {
    * Event::create([
        * 'event_date' => Carbon::now(),
        * 'title' => ['hu' => 'Csapatértekezlet'],
        * 'slug' => [], // No slug translations
        * 'status' => EventStatus::PUBLISHED->value,
    * ]);
 *
* Livewire::test(EventCalendar::class)
        * ->assertSee('Csapatértekezlet')
        * ->assertSeeHtml('href="http://localhost/events/#"'); // Fallback to '#'
* });
 *
* it('displays events with null start_time correctly', function () {
    * Event::create([
        * 'event_date' => Carbon::now(),
        * 'title' => ['de' => 'Event ohne Zeit', 'hu' => 'Idő nélküli esemény'],
        * 'slug' => ['de' => 'event-ohne-zeit', 'hu' => 'ido-nelkuli-esemeny'],
        * 'status' => EventStatus::PUBLISHED->value,
    * ]);
 *
* Livewire::test(EventCalendar::class)
        * ->assertSee('Event ohne Zeit')
        * ->assertSeeHtml('aria-label="set default time"'); // No time displayed
* });
 *
* it('displays today’s date with correct styling', function () {
    * Livewire::test(EventCalendar::class)
        * ->assertSeeHtml('class="flex size-6 items-center justify-center rounded-full bg-emerald-600 font-semibold text-white"')
        * ->assertSeeHtml('aria-current="date"');
* });
 *
* it('displays days from previous and next months correctly', function () {
    * $prevMonthDay = Carbon::now()->subMonth()->endOfMonth()->day;
    * $nextMonthDay = 1;
 *
* Livewire::test(EventCalendar::class)
        * ->assertSeeHtml('class="relative px-3 py-2 min-h-28 bg-gray-50 text-gray-500"')
        * ->assertSee($prevMonthDay)
        * ->assertSee($nextMonthDay);
* });
 *
* it('renders flux button for event details', function () {
    * Event::create([
        * 'event_date' => Carbon::now(),
        * 'title' => ['de' => 'Teamtreffen', 'hu' => 'Csapatértekezlet'],
        * 'slug' => ['de' => 'teamtreffen', 'hu' => 'csapatertekezlet'],
        * 'status' => EventStatus::PUBLISHED->value,
    * ]);
 *
* Livewire::test(EventCalendar::class)
        * ->assertSeeHtml('<a href="http://localhost/events/teamtreffen" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-8 text-sm rounded-md px-3 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)] [[data-flux-button-group]_&amp;]:border-e-0 [:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-e-[1px] dark:[:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-e-0 dark:[:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-s-[1px] [:is([data-flux-button-group]&gt;&amp;:not(:first-child),_[data-flux-button-group]_:not(:first-child)&gt;&amp;)]:border-s-[color-mix(in_srgb,var(--color-accent-foreground),transparent_85%)]" data-flux-button="data-flux-button" data-flux-group-target="data-flux-group-target">')
        * ->assertSee('Details');
* });
 *
* it('displays events in correct locale based on app locale', function () {
 *
* // Set session locale to mimic middleware
    * session()->put('locale', 'hu');
    * app()->setLocale(session('locale')); // Apply middleware logic
 *
* Event::create([
        * 'event_date' => Carbon::now(),
        * 'title' => ['de' => 'Teamtreffen', 'hu' => 'Csapatértekezlet'],
        * 'slug' => ['de' => 'teamtreffen', 'hu' => 'csapatertekezlet'],
        * 'status' => EventStatus::PUBLISHED->value,
    * ]);
 *
* Livewire::test(EventCalendar::class)
        * ->assertSee('Csapatértekezlet')
        * ->assertDontSee('Teamtreffen')
        * ->assertSeeHtml('href="'.route('events.show', 'csapatertekezlet').'"');
* });
 *

 **/
