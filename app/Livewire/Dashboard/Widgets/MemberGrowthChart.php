<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Enums\MemberType;
use App\Livewire\Concerns\HasDriverAwareDateExpressions;
use App\Models\Membership\Member;
use Illuminate\Support\Carbon;
use Livewire\Component;

class MemberGrowthChart extends Component
{
    use HasDriverAwareDateExpressions;

    public string $period = 'month'; // week | month | year | all

    /** Chartdaten für flux:chart – [['date' => ..., 'members' => ...], ...] */
    public array $data = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function updatedPeriod(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        // Aktive Mitglieder (kein Bewerber, nicht ausgetreten)
        $activeTypes = [
            MemberType::MD->value,
            MemberType::AD->value,
            // ggf. weitere aktive Typen ergänzen
        ];

        [$from, $granularity] = match ($this->period) {
            'week'  => [Carbon::now()->subDays(6)->startOfDay(),  'day'],
            'month' => [Carbon::now()->subDays(29)->startOfDay(), 'day'],
            'year'  => [Carbon::now()->startOfYear(),             'month'],
            default => [null,                                     'month'],
        };

        // PHP-Format für die lückenlose Zeitreihe
        $phpFormat = $granularity === 'day' ? 'Y-m-d' : 'Y-m';

        // DB-Expression je Granularität (treiber-agnostisch)
        $expr = $granularity === 'day'
            ? $this->exprDay('entered_at')
            : $this->exprMonth('entered_at');

        // Basis-Query
        $baseQuery = Member::query()
            ->whereIn('type', $activeTypes)
            ->whereNotNull('entered_at')
            ->whereNull('left_at');

        // Neue Mitglieder pro Periode im gewählten Zeitraum
        $periodQuery = (clone $baseQuery);
        if ($from) {
            $periodQuery->where('entered_at', '>=', $from);
        }

        $rawData = $periodQuery
            ->selectRaw("{$expr} as period, COUNT(*) as new_members")
            ->groupByRaw($expr)
            ->orderBy('period')
            ->get();

        // Bestand VOR dem Zeitraum als Startwert der kumulativen Linie
        $baseCount = $from
            ? (clone $baseQuery)->where('entered_at', '<', $from)->count()
            : 0;

        // Lückenlose Zeitreihe aufbauen und Bestand kumulieren
        $series  = $this->buildTimeSeries($from, $phpFormat);
        $lookup  = $rawData->keyBy('period');
        $running = $baseCount;

        $this->data = collect($series)
            ->map(function (string $key) use ($lookup, &$running): array {
                $running += (int) ($lookup[$key]->new_members ?? 0);

                return ['date' => $key, 'members' => $running];
            })
            ->values()
            ->toArray();
    }

    /**
     * Lückenlose Reihe aller Perioden von $from bis heute.
     * Bei period='all' beginnt die Reihe beim frühesten entered_at.
     */
    private function buildTimeSeries(?Carbon $from, string $phpFormat): array
    {
        $now    = Carbon::now();
        $series = [];

        if ($phpFormat === 'Y-m') {
            $earliest = $from
                ?? Carbon::parse(
                    Member::query()->whereNotNull('entered_at')->min('entered_at')
                    ?? $now->copy()->subYears(5)->toDateString()
                );

            $cursor = $earliest->copy()->startOfMonth();

            while ($cursor->lte($now)) {
                $series[] = $cursor->format($phpFormat);
                $cursor->addMonth();
            }
        } else {
            $cursor = ($from ?? $now->copy()->subDays(29))->copy()->startOfDay();

            while ($cursor->lte($now)) {
                $series[] = $cursor->format($phpFormat);
                $cursor->addDay();
            }
        }

        return $series;
    }

    public function render()
    {
        return view('livewire.dashboard.widgets.member-growth-chart');
    }
}