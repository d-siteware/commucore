<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Global;

use App\Actions\Global\CreateLocale;
use App\Actions\Global\UpdateLocale;
use App\Models\Locale;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class LocaleForm extends Form
{
    protected ?Locale $locale = null;

    public ?int $id = null;

    public ?bool $active = false;

    public ?string $label;

    public ?string $name;

    public ?string $decimal_separator;

    public ?string $thousands_separator;

    public ?string $currency_symbol;

    public ?string $currency_position;

    public ?string $name_order;

    public ?string $date_format;

    public function set(int $id): void
    {
        $locale = Locale::findOrFail($id);
        $this->locale = $locale;
        $this->id = $locale->id;
        $this->active = $locale->active;
        $this->label = $locale->label;
        $this->name = $locale->name;
        $this->decimal_separator = $locale->decimal_separator;
        $this->thousands_separator = $locale->thousands_separator;
        $this->currency_symbol = $locale->currency_symbol;
        $this->currency_position = $locale->currency_position;
        $this->name_order = $locale->name_order;
        $this->date_format = $locale->date_format;
    }

    public function create(): Locale
    {
        return CreateLocale::handle($this);
    }

    public function update(): Locale
    {
        $locale = Locale::findOrFail($this->id);

        return UpdateLocale::handle($this, $locale);
    }

    public function delete(): bool
    {
        $locale = Locale::findOrFail($this->id);

        return $locale->delete();
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('locales', 'name')->ignore($this->id),
            ],
            'label' => 'required|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Name ist erforderlich.',
            'name.unique' => 'Diese Sprache existiert bereits.',
            'label.required' => 'Label ist erforderlich.',
        ];
    }
}
