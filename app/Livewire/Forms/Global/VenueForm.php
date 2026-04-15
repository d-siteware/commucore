<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Global;

use App\Models\Venue;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class VenueForm extends Form
{
    public ?Venue $venue = null;

    public int $id;

    public string $name = '';

    public string $address = '';

    public string $city = '';

    public string $country = '';

    public string $postal_code = '';

    public string $phone = '';

    public string $website = '';

    public string $geolocation = '';

    public function setVenue(Venue $venue): void
    {
        $this->venue = $venue;
        $this->id = $venue->id;
        $this->name = $venue->name;
        $this->address = $venue->address;
        $this->city = $venue->city ?? '';
        $this->country = $venue->country ?? '';
        $this->postal_code = $venue->postal_code ?? '';
        $this->phone = $venue->phone ?? '';
        $this->website = $venue->website ?? '';
        $this->geolocation = $venue->geolocation ?? '';
    }

    public function resetForm(): void
    {
        $this->venue = null;
        $this->id = 0;
        $this->name = '';
        $this->address = '';
        $this->city = '';
        $this->country = '';
        $this->postal_code = '';
        $this->phone = '';
        $this->website = '';
        $this->geolocation = '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('venues','name')->ignore($this->id ?? null),
            ],
            'address' => ['required', 'string'],
        ];
    }

    public function store(): int
    {
        $this->validate();

        $venue = new Venue;

        $venue->name = $this->name;
        $venue->address = $this->address;
        $venue->city = $this->city;
        $venue->country = $this->country;
        $venue->postal_code = $this->postal_code;
        $venue->phone = $this->phone;
        $venue->website = $this->website;
        $venue->geolocation = $this->geolocation;

        return $venue->save() ? $venue->id : 0;
    }

    public function update(): bool
    {
        $this->validate();

        if ($this->venue === null) {
            return false;
        }

        return $this->venue->update([
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'website' => $this->website,
            'geolocation' => $this->geolocation,
        ]);
    }
}