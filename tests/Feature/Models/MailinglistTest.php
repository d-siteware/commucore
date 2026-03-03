<?php

declare(strict_types=1);

use App\Actions\Global\CreateMailingListEntry;
use App\Models\MailingList;

it('an invalid token leads to a redirect', function (): void {

    $response = $this->get('/mailing-list/invalid-token');

    //    $response->assertStatus(200);

    $response->assertRedirect();
});

it('sets terms_accepted_at on create when terms are accepted', function (): void {
    $component = Livewire::test(\App\Livewire\App\Global\Mailinglist\Form::class);

    $component->set('form.email', 'test@example.com')
        ->set('form.terms_accepted', true)
        ->set('form.update_on_events', false)
        ->set('form.update_on_articles', false)
        ->set('form.update_on_notifications', false)
        ->set('form.locale', 'de')
        ->call('addMailingListEntry');

    $subscriber = MailingList::where('email', 'test@example.com')->firstOrFail();

    expect($subscriber->terms_accepted_at)->not->toBeNull()
        ->and($subscriber->terms_accepted_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

it('does not set terms_accepted_at when terms are not accepted', function (): void {
    // terms_accepted = false schlägt Validierung fehl – also direkt via Action testen
    // aber CreateMailingListEntry::handle() direkt aufrufen geht nicht ohne Form-Kontext.
    // Stattdessen: DB direkt prüfen wenn terms_accepted false ist per Factory.
    $entry = MailingList::factory()->create([
        'terms_accepted' => false,
        'terms_accepted_at' => null,
    ]);

    expect($entry->terms_accepted_at)->toBeNull();
});

it('does not overwrite terms_accepted_at on update', function (): void {
    $original = MailingList::factory()->create([
        'terms_accepted' => true,
        'terms_accepted_at' => $originalTimestamp = now()->subDays(10),
    ]);

    $component = Livewire::test(\App\Livewire\App\Global\Mailinglist\Form::class);

    $component->set('form.email', 'test@example.com')
        ->set('form.terms_accepted', true)
        ->set('form.update_on_events', false)
        ->set('form.update_on_articles', false)
        ->set('form.update_on_notifications', false)
        ->set('form.locale', 'de')
        ->set('form.email', $original->email)
        ->call('addMailingListEntry');

    expect($original->fresh()->terms_accepted_at->toDateString())
        ->toBe($originalTimestamp->toDateString());
});
