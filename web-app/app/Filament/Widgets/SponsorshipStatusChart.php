<?php

namespace App\Filament\Widgets;

use App\Models\Sponsorship;
use Filament\Widgets\ChartWidget;

class SponsorshipStatusChart extends ChartWidget
{
    public static function canView(): bool
    {
        return str_contains(request()->url(), 'analytics-dashboard');
    }

    protected static ?string $heading = 'حالة الكفالات (نشطة / متوقفة)';

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => 'الحالة',
                'data' => [
                    Sponsorship::where('status', 'active')->count(),
                    Sponsorship::where('status', 'stopped')->count(),
                ],
                'backgroundColor' => ['#22c55e', '#ef4444'],
            ]],
            'labels' => ['نشطة', 'متوقفة'],
        ];
    }

    protected function getType(): string { return 'doughnut'; }
}