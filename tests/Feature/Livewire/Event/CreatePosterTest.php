<?php

declare(strict_types=1);

use App\Livewire\Activity\Event\PosterGenerator\Create;
use App\Models\Membership\Member;
use App\Models\User;
use Tests\Traits\TranslationTestTrait;

uses(TranslationTestTrait::class);

test('can create jpeg poster from template', function (): void {

    Livewire::test(Create::class)
        ->assertSeeLivewire(Create::class) // Ensures Livewire renders
        ->assertStatus(200);

});

test('can create pdf poste from template', function (): void {

    Livewire::test(Create::class)
        ->assertSeeLivewire(Create::class) // Ensures Livewire renders
        ->assertStatus(200);

});

test('if all translations are rendered on backend event index page', function (): void {
    $this->actingAs(Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user);

    $event = \App\Models\Event\Event::factory()->create();

    $this->assertTranslationsRendered(
        Create::class, [],
        'event',
        'event.',
    );
});
