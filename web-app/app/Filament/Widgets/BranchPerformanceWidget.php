<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Sponsorship;
use Filament\Widgets\ChartWidget;

class BranchPerformanceWidget extends ChartWidget
{
    protected static ?string $heading = 'مقارنة أداء الفروع';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return str_contains(request()->url(), 'analytics-dashboard');
    }

    protected function getData(): array
    {
        $branches = Branch::withCount('orphans')->get();
        $activeSponsorships = [];
        $inactiveSponsorships = [];

        foreach ($branches as $branch) {
            $total = Sponsorship::whereHas('orphan', fn($q) => $q->where('branch_id', $branch->id))->count();
            $active = Sponsorship::whereHas('orphan', fn($q) => $q->where('branch_id', $branch->id))
                ->where('status', 'active')->count();
            $activeSponsorships[] = $active;
            $inactiveSponsorships[] = $total - $active;
        }

        return [
            'datasets' => [
                [
                    'label' => 'كفالات نشطة',
                    'data' => $activeSponsorships,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#16a34a',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'كفالات متوقفة/بدون',
                    'data' => $inactiveSponsorships,
                    'backgroundColor' => '#f87171',
                    'borderColor' => '#dc2626',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $branches->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'beginAtZero' => true],
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
