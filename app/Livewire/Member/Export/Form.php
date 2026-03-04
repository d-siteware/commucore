<?php

declare(strict_types=1);

namespace App\Livewire\Member\Export;

use App\Enums\ExportType;
use App\Enums\MemberType;
use App\Services\Export\MemberExportQuery;
use Livewire\Component;

final class Form extends Component
{
    public string $exportType;

    public bool $includePs = false;   // include_pseudonymized

    public bool $onlyActive = false;

    /** @var string[] */
    public array $memberTypes = [];

    public int $previewCount = 0;

    public function mount(): void
    {
        $this->updatePreviewCount();
        $this->exportType = ExportType::STAMMDATEN->value;
    }

    public function updatedExportType(): void
    {
        $this->updatePreviewCount();
    }

    public function updatedIncludePs(): void
    {
        $this->updatePreviewCount();
    }

    public function updatedOnlyActive(): void
    {
        $this->updatePreviewCount();
    }

    public function updatedMemberTypes(): void
    {
        $this->updatePreviewCount();
    }

    private function updatePreviewCount(): void
    {
        $this->previewCount = MemberExportQuery::build([
            'include_pseudonymized' => $this->includePs,
            'only_active' => $this->onlyActive,
            'member_types' => $this->memberTypes,
        ])->count();
    }

    /** @return array<string, mixed> */
    public function exportParams(): array
    {
        return [
            'export_type' => $this->exportType,
            'include_pseudonymized' => $this->includePs ? '1' : '0',
            'only_active' => $this->onlyActive ? '1' : '0',
            'member_types' => $this->memberTypes,
        ];
    }

    public function currentExportTypeLabel(): string
    {
        return ExportType::from($this->exportType)->label();
    }

    public function exportTypes(): array
    {
        return ExportType::cases();
    }

    public function memberTypeOptions(): array
    {
        return MemberType::cases();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.export.form');
    }
}
