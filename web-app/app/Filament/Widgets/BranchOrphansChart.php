<?php

namespace App\Filament\Resources\BranchResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Branch;

class BranchOrphansChart extends ChartWidget
{
    protected static ?string $heading = 'إحصائيات الأيتام حسب الفروع';

    protected function getData(): array
    {
        // جلب الفروع وعدد الأيتام في كل فرع للمخطط البياني
        $branches = Branch::withCount('orphans')->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الأيتام',
                    'data' => $branches->pluck('orphans_count')->toArray(),
                    'backgroundColor' => ['#34d399', '#60a5fa', '#fbbf24', '#f87171', '#a78bfa'],
                ],
            ],
            'labels' => $branches->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // نوع المخطط الدائري المميز
    }
}