<?php

declare(strict_types=1);

namespace App\Livewire\Member\Import\Steps;

use App\Services\Import\MemberFieldMapper;
use Livewire\Component;

final class MappingStep extends Component
{
    /** @var string[] */
    public array $csvHeaders = [];

    /** @var array<int, array<string, string>> */
    public array $rows = [];

    /** csvHeader → commuCoreField */
    /** @var array<string, string> */
    public array $fieldMap = [];

    /** field → [unknownValue → commuCoreValue] */
    /** @var array<string, array<string, string>> */
    public array $enumMap = [];

    /** @var array<string, string[]> */
    public array $unknownEnumValues = [];

    public bool $showEnumModal = false;

    public string $importCacheKey = '';

    public function mount(): void
    {
        $cached = \Cache::get($this->importCacheKey, []);

        $this->csvHeaders = $cached['headers'] ?? $this->csvHeaders;
        $this->rows = $cached['all_rows'] ?? [];

        $analysis = MemberFieldMapper::analyse($this->csvHeaders);

        foreach ($this->csvHeaders as $header) {
            $this->fieldMap[$header] = $analysis['auto_mapped'][$header] ?? '';
        }
    }

    public function updatedFieldMap(): void
    {
        $this->detectUnknownEnums();
    }

    public function confirmMapping(): void
    {
        // Unbekannte Enum-Werte prüfen
        $this->detectUnknownEnums();

        if ($this->unknownEnumValues !== []) {
            $this->showEnumModal = true;

            return;
        }

        $this->finishMapping();
    }

    public function confirmEnumMapping(): void
    {
        $this->showEnumModal = false;
        $this->finishMapping();
    }

    private function detectUnknownEnums(): void
    {
        // Nur gemappte Felder prüfen
        $activeMap = array_filter(
            $this->fieldMap,
            static fn (string $v): bool => $v !== '',
        );

        $this->unknownEnumValues = MemberFieldMapper::detectUnknownEnumValues(
            $this->rows,
            $activeMap,
        );
    }

    private function finishMapping(): void
    {
        $mappedRows = array_map(
            fn (array $row): array => MemberFieldMapper::applyMapping($row, $this->fieldMap),
            $this->rows,
        );

        if ($this->enumMap !== []) {
            $mappedRows = MemberFieldMapper::applyEnumMapping($mappedRows, $this->enumMap);
        }

        \Cache::put($this->importCacheKey.'_mapped', $mappedRows, now()->addHour());

        $this->dispatch('mapping-complete', data: [
            'field_map' => $this->fieldMap,
            'enum_map' => $this->enumMap,
        ]);
    }

    public function commuCoreFieldOptions(): array
    {
        return array_merge(['' => '— ignorieren —'], MemberFieldMapper::MEMBER_FIELDS);
    }

    public function enumOptions(string $field): array
    {
        return MemberFieldMapper::enumOptions($field);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.import.steps.mapping-step');
    }
}
