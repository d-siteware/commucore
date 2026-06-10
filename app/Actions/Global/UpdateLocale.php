<?php

declare(strict_types=1);

namespace App\Actions\Global;

use App\Livewire\Forms\Global\LocaleForm;
use App\Models\Locale;
use Illuminate\Support\Facades\DB;

final class UpdateLocale
{
    public static function handle(LocaleForm $form, Locale $locale): Locale
    {
        return DB::transaction(function () use ($form, $locale) {
            $locale->update([
                'active' => $form->active ?? false,
                'label' => $form->label,
                'name' => $form->name,
                'decimal_separator' => $form->decimal_separator ?? ',',
                'thousands_separator' => $form->thousands_separator ?? '.',
                'currency_symbol' => $form->currency_symbol ?? 'EUR',
                'currency_position' => $form->currency_position ?? 'before',
                'name_order' => $form->name_order ?? 'first_last',
                'date_format' => $form->date_format ?? 'DD.MM.JJJJ',
            ]);

            return $locale->fresh();
        });
    }
}
