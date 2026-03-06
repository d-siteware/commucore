<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Enums\Locale;
use App\Models\User;
use Illuminate\Support\Arr;
use Livewire\Livewire;

trait TranslationTestTrait
{
    /**
     * Assert that all translation keys are rendered in a component
     *
     * @param  string  $componentClass  The Livewire component class
     * @param  array  $componentParams  Parameters to pass to the component
     * @param  string  $translationFile  The translation file to check
     * @param  string  $prefix  Optional prefix for nested translations
     * @param  bool  $strictMode  Whether to ensure no translation keys are visible
     */
    public function assertTranslationsRendered(
        string $componentClass,
        array $componentParams = [],
        string $translationFile = 'event',
        string $prefix = 'event.',
        bool $strictMode = true,
    ): void {
        // Authenticate an admin user (adjust as needed)
        if (empty($componentParams['user'])) {
            $user = \App\Models\User::factory()
                ->create(['is_admin' => true]);
            $this->actingAs($user);
        }

        // Collect all translation keys across defined locales
        $keys = [];
        foreach (Locale::cases() as $locale) {
            $translations = require "lang/{$locale->value}/{$translationFile}.php";
            $keys = array_merge($keys, array_keys(Arr::dot($translations, $prefix)));
        }

        if (Livewire::isDiscoverable($componentClass)) {

            // Use Livewire::test with explicit parameter passing
            $component = Livewire::test($componentClass, $componentParams);
            // Assert translations are handled correctly
            foreach ($keys as $key) {
                if ($key !== $prefix) {
                    if ($strictMode) {
                        $component->assertDontSee($key);
                    } else {
                        $this->assertComponentKeyReplaced($component, $key);
                    }
                }
            }

            return;
        }

        // Controller method handling
        $controllerInstance = new $componentClass;
        $method = $componentParams['method'] ?? 'index';

        // Call the method
        $view = $controllerInstance->{$method}();

        // Ensure it's a view
        $this->assertInstanceOf(\Illuminate\View\View::class, $view);

        // Render the view
        $renderedView = $view->render();

        // Check translation keys
        foreach ($keys as $key) {
            if ($key !== $prefix) {
                if ($strictMode) {
                    // In strict mode, ensure the raw translation key is not present
                    $this->assertStringNotContainsString($key, $renderedView,
                        "Translation key '$key' should be replaced");
                } else {
                    // Ensure the key is not a direct match (suggesting it's been translated)
                    $this->assertFalse(
                        Str::contains($renderedView, $key),
                        "Translation key '$key' should be replaced"
                    );
                }
            }
        }
    }

    /**
     * Optional helper method to check if a translation key is replaced
     *
     * @param  mixed  $component  The tested component
     * @param  string  $key  The translation key to check
     */
    protected function assertComponentKeyReplaced($component, string $key): void
    {
        $translatedValue = __($key);

        // Ensure the original key is not visible and the translated value is
        $component
            ->assertDontSee($key)
            ->assertSee($translatedValue);
    }

    // Helper method to check if a key is replaced (similar to Livewire approach)
    protected function assertKeyReplaced($component, string $key): void
    {
        $this->assertFalse(
            Str::contains($component->rendered(), $key),
            "Translation key '$key' should be replaced"
        );
    }
}
