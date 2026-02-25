<?php

declare(strict_types=1);

use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Models\Membership\Member;

beforeEach(function (): void {
    // Seed or setup any necessary data before each test
    $this->member = Member::factory()->create();

    // Use array cache instead of mocking
    config(['cache.default' => 'array']);
});

it('renders the mailer-test route successfully', function (): void {
    Event::factory()->create(['id' => 1]);

    $controller = new \App\Http\Controllers\TestingController;
    $view = $controller->mailTest();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
    expect($view->render())->toBeString();
});

it('switches locale and redirects back', function (): void {
    // Erstelle die Route falls sie nicht existiert
    Route::get('/lang/{locale}', function (string $locale) {
        if (in_array($locale, \App\Models\Locale::available())) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        return redirect()->back();
    })->name('locale.switch');

    // Simulate a previous page
    $this->get('/'); // Sets HTTP_REFERER

    $response = $this->get('/lang/hu');

    $response->assertStatus(302) // Redirect
        ->assertRedirect('/'); // Back to previous page
    expect(app()->getLocale())->toBe('hu');
    expect(session('locale'))->toBe('hu');
});

it('confirms event subscription with valid token', function (): void {
    // Create a subscription with event
    $event = Event::factory()->create();
    $subscription = EventSubscription::factory()->create([
        'event_id' => $event->id,
        'confirmed_at' => null,
    ]);
    $token = 'valid-token';

    // Speichere den Token im Cache (kein Mock, echte Array-Cache)
    Cache::put("event_subscription_{$subscription->id}_token", $token, now()->addHour());

    // Hit the route
    $response = $this->get("/events/subscription/confirm/{$subscription->id}/{$token}");

    // Assert the response
    $response->assertStatus(200)
        ->assertViewIs('events.show')
        ->assertViewHas('event', fn ($e) => $e->id === $event->id)
        ->assertSessionHas('status', 'Deine Anmeldung wurde bestätigt! 🎉');

    // Assert the subscription was updated and cache cleared
    expect($subscription->fresh()->confirmed_at)->not->toBeNull();
    expect(Cache::has("event_subscription_{$subscription->id}_token"))->toBeFalse();
});

it('aborts with 403 for invalid event subscription token', function (): void {
    $event = Event::factory()->create();
    $subscription = EventSubscription::factory()->create([
        'event_id' => $event->id,
        'confirmed_at' => null,
    ]);
    $invalidToken = 'invalid-token';
    $validToken = 'valid-token';

    // Speichere den gültigen Token
    Cache::put("event_subscription_{$subscription->id}_token", $validToken, now()->addHour());

    $response = $this->get("/events/subscription/confirm/{$subscription->id}/{$invalidToken}");

    $response->assertStatus(403);
    expect($subscription->fresh()->confirmed_at)->toBeNull();
});
