<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/
//
// pest()->extend(Tests\TestCase::class)
//    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
//    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

beforeEach(function (): void {
    //  Queue::fake(); // Optional: fake queue to inspect jobs
    //  config(['queue.default' => 'sync']); // Run jobs immediately
});

function something(): void
{
    // ..
}

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Feature\Models');

/*
function assertTranslationsRendered($componentClass, $langFile, $prefix): void
{
    $user = \App\Models\User::factory()
        ->create(['is_admin' => true]);
    test()->actingAs($user);

    $component = Livewire::test($componentClass);

    $keys = [];
    foreach (\App\Enums\Locale::cases() as $locale) {
        $translations = require "lang/{$locale->value}/{$langFile}";
        $keys = array_merge($keys, array_keys(Arr::dot($translations, $prefix)));
    }

    foreach ($keys as $key) {
        if ($key !== $prefix) {
            $component->assertDontSee($key);
        }
    }
}*/

use RyanChandler\LaravelCloudflareTurnstile\Facades\Turnstile;

beforeEach(function (): void {
    // This forces a success response for *all* validations in tests
    Turnstile::fake();
});
