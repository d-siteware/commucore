<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Enums\FundingStatus;
use App\Models\Funding\Funding;
use Livewire\Component;

final class FundingWidget extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        $currentYear = (int) now()->format('Y');

        $fundings = Funding::query()
            ->inYear($currentYear)
            ->whereIn('status', [FundingStatus::Active])
            ->with(['projects' => fn ($q) => $q->withPivot('allocated_amount')])
            ->orderBy('funding_period_end')
            ->get()
            ->map(function (Funding $funding) {
                $approved  = $funding->approved_amount ?? 0;
                $received  = $funding->totalReceived();
                $allocated = (int) $funding->projects()->sum('project_fundings.allocated_amount');
                $remaining = $approved - $allocated;

                $receivedRate  = $approved > 0 ? min(100, round($received  / $approved * 100)) : 0;
                $allocatedRate = $approved > 0 ? min(100, round($allocated / $approved * 100)) : 0;

                // Warnung: Förderzeitraum läuft in < 60 Tagen ab
                $expiresWarning = $funding->funding_period_end
                    && $funding->funding_period_end->diffInDays(now(), false) > -60
                    && $funding->funding_period_end->isFuture();

                return [
                    'id'             => $funding->id,
                    'title'          => $funding->title,
                    'funder'         => $funding->funder,
                    'approved'       => $approved,
                    'received'       => $received,
                    'allocated'      => $allocated,
                    'remaining'      => $remaining,
                    'received_rate'  => (int) $receivedRate,
                    'allocated_rate' => (int) $allocatedRate,
                    'period_end'     => $funding->funding_period_end?->format('d.m.Y'),
                    'expires_soon'   => $expiresWarning,
                ];
            });

        $totalApproved  = $fundings->sum('approved');
        $totalReceived  = $fundings->sum('received');
        $totalAllocated = $fundings->sum('allocated');
        $totalRemaining = $totalApproved - $totalAllocated;

        return view('livewire.dashboard.widgets.funding-widget', [
            'fundings'       => $fundings,
            'totalApproved'  => $totalApproved,
            'totalReceived'  => $totalReceived,
            'totalAllocated' => $totalAllocated,
            'totalRemaining' => $totalRemaining,
        ]);
    }
}