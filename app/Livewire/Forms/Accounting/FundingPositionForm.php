<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Models\Funding\Funding;
use App\Models\Funding\FundingPosition;
use Livewire\Form;

final class FundingPositionForm extends Form
{
    public ?int $id = null;

    public string $title = '';

    public string $budget = '';

    public ?int $funding_position_category_id = null;

    public ?int $member_id = null;

    public string $due_date = '';

    public string $description = '';

    public function setPosition(FundingPosition $position): void
    {
        $this->id = $position->id;
        $this->title = $position->title;
        $this->budget = \App\Helpers\MoneyHelper::formatCents($position->budget, withSymbol: false);
        $this->funding_position_category_id = $position->funding_position_category_id;
        $this->member_id = $position->member_id;
        $this->due_date = $position->due_date?->format('Y-m-d') ?? '';
        $this->description = $position->description ?? '';
    }

    public function store(Funding $funding): FundingPosition
    {
        $validated = $this->validate();

        /** @var FundingPosition */
        return $funding->fundingPositions()->create([
            'title' => $validated['title'],
            'budget' => $this->parseCents($validated['budget']) ?? 0,
            'funding_position_category_id' => $validated['funding_position_category_id'] ?: null,
            'member_id' => $validated['member_id'] ?: null,
            'due_date' => $validated['due_date'] ?: null,
            'description' => $validated['description'] ?: null,
        ]);
    }

    public function update(FundingPosition $position): void
    {
        $validated = $this->validate();

        $position->update([
            'title' => $validated['title'],
            'budget' => $this->parseCents($validated['budget']) ?? 0,
            'funding_position_category_id' => $validated['funding_position_category_id'] ?: null,
            'member_id' => $validated['member_id'] ?: null,
            'due_date' => $validated['due_date'] ?: null,
            'description' => $validated['description'] ?: null,
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
            'budget' => ['nullable', 'string'],
            'funding_position_category_id' => ['nullable', 'integer', 'exists:funding_position_categories,id'],
            'member_id' => ['nullable', 'integer', 'exists:members,id'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
