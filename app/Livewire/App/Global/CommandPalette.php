<?php

namespace App\Livewire\App\Global;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class CommandPalette extends Component
{
    public bool $open = false;

    public string $query = '';

    /** @return array<int, array{id: int, label: string, meta: string, url: string, type: string}> */
    public function results(): array
    {
        if (strlen($this->query) < 1) {
            return [];
        }

        $q = strtolower(ltrim($this->query));
        $prefix = $q[0] ?? '';
        $search = strtolower(ltrim($q, '~>#'));

        return match ($prefix) {
            '~' => $this->search('members', $search),
            '>' => $this->search('events', $search),
            '#' => $this->search('transactions', $search),
            default => array_merge(
                $this->search('members', $search),
                $this->search('events', $search),
                $this->search('transactions', $search),
            ),
        };
    }

    /**
     * @return array<int, array{id: int, label: string, meta: string, url: string, type: string}>
     */
    private function search(string $type, string $search): array
    {
        $items = Cache::tags(['palette', $type])->get('palette:'.$type, []);

        return collect($items)
            ->filter(fn ($item) => str_contains(strtolower($item['label']), $search)
                || str_contains(strtolower($item['meta'] ?? ''), $search))
            ->take(5)
            ->map(fn ($item) => [...$item, 'type' => $type])
            ->values()
            ->all();
    }

    #[On('open-palette')]
    public function openPalette(): void
    {
        $this->open = true;
        $this->query = '';
    }

    public function close(): void
    {
        $this->open = false;
        $this->query = '';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.global.command-pallete', [
            'results' => $this->results(),
        ]);
    }
}
