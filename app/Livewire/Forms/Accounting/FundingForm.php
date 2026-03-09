<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Models\Funding\Funding;
use Livewire\Form;

final class FundingForm extends Form
{
    public ?int $id = null;

    public string $title = '';

    public string $funder = '';

    public string $reference = '';

    public string $status = '';

    public string $description = '';

    public string $approved_amount = '';

    public string $funding_period_start = '';

    public string $funding_period_end = '';

    public function setFunding(Funding $funding): void
    {
        $this->id = $funding->id;
        $this->title = $funding->title;
        $this->funder = $funding->funder ?? '';
        $this->reference = $funding->reference ?? '';
        $this->status = $funding->status->value;
        $this->description = $funding->description ?? '';
        $this->approved_amount = $funding->approved_amount
            ? number_format($funding->approved_amount / 100, 2, ',', '.')
            : '';
        $this->funding_period_start = $funding->funding_period_start?->format('Y-m-d') ?? '';
        $this->funding_period_end = $funding->funding_period_end?->format('Y-m-d') ?? '';
    }

    public function store(): Funding
    {
        $validated = $this->validate();

        return Funding::create([
            'title' => $validated['title'],
            'funder' => $validated['funder'] ?: null,
            'reference' => $validated['reference'] ?: null,
            'status' => $validated['status'],
            'description' => $validated['description'] ?: null,
            'approved_amount' => $this->parseCents($validated['approved_amount']),
            'funding_period_start' => $validated['funding_period_start'] ?: null,
            'funding_period_end' => $validated['funding_period_end'] ?: null,
        ]);
    }

    public function update(Funding $funding): void
    {
        $validated = $this->validate();

        $funding->update([
            'title' => $validated['title'],
            'funder' => $validated['funder'] ?: null,
            'reference' => $validated['reference'] ?: null,
            'status' => $validated['status'],
            'description' => $validated['description'] ?: null,
            'approved_amount' => $this->parseCents($validated['approved_amount']),
            'funding_period_start' => $validated['funding_period_start'] ?: null,
            'funding_period_end' => $validated['funding_period_end'] ?: null,
        ]);
    }

    private function parseCents(string $value): ?int
    {
        if ($value === '') {
            return null;
        }

        // Handle German number format: 1.234,56 → 123456
        $normalized = str_replace(['.', ','], ['', '.'], $value);

        return (int) round((float) $normalized * 100);
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'funder' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'approved_amount' => ['nullable', 'string'],
            'funding_period_start' => ['nullable', 'date'],
            'funding_period_end' => ['nullable', 'date', 'after_or_equal:funding_period_start'],
        ];
    }
}
