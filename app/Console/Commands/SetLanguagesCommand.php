<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Locale;
use Illuminate\Console\Command;

final class SetLanguagesCommand extends Command
{
    protected $signature = 'commucore:set-languages
        {locales* : Sprach-Codes, z. B. de en hu}';

    protected $description = 'Aktiviert die angegebenen Sprachen und deaktiviert alle anderen.';

    public function handle(): int
    {
        /** @var array<int, string> $requested */
        $requested = (array) $this->argument('locales');

        $available = Locale::available();

        $unknown = array_diff($requested, $available);
        if (! empty($unknown)) {
            $this->error('Unbekannte Sprachen: '.implode(', ', $unknown).'. Verfügbar: '.implode(', ', $available));

            return self::FAILURE;
        }

        // de immer erzwingen
        if (! in_array('de', $requested, true)) {
            $requested[] = 'de';
            $this->warn('Deutsch (de) wurde automatisch als Pflichtsprache hinzugefügt.');
        }

        Locale::query()->update(['active' => false]);
        Locale::whereIn('name', $requested)->update(['active' => true]);

        $this->info('Aktive Sprachen: '.implode(', ', $requested));

        return self::SUCCESS;
    }
}
