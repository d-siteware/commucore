<?php

declare(strict_types=1);

use App\Livewire\Activity\Event\Visitor\Create\Form;
use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Models\Event\EventVisitor;
use App\Models\User;
use Livewire\Livewire;

it('only offers and accepts subscriptions from the current event', function (): void {
    $user = User::factory()->admin()->create();
    $event = Event::factory()->create();
    $otherEvent = Event::factory()->create();
    $subscription = EventSubscription::factory()->create([
        'event_id' => $event->id,
        'name' => 'Current event signup',
    ]);
    $otherSubscription = EventSubscription::factory()->create([
        'event_id' => $otherEvent->id,
        'name' => 'Other event signup',
    ]);

    Livewire::actingAs($user)
        ->test(Form::class, ['event' => $event])
        ->assertSee($subscription->name)
        ->assertDontSee($otherSubscription->name)
        ->set('form.name', 'Manual visitor')
        ->set('form.event_subscription_id', $otherSubscription->id)
        ->call('add')
        ->assertHasErrors(['form.event_subscription_id' => 'exists']);

    expect(EventVisitor::query()->count())->toBe(0);
});
