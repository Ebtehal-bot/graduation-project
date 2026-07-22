<?php

namespace App\Filament\Widgets;

use App\Models\Sponsorship;
use Filament\Widgets\ChartWidget;

class SponsorshipTypeChart extends ChartWidget
{
    public static function canView(): bool
    {
        return str_contains(request()->url(), 'analytics-dashboard');
    }

    protected static ?string $heading = 'تحليل توزيع أنواع الكفالات';

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => 'عدد الكفالات',
                'data' => [
                    Sponsorship::where('sponsorship_type', 'financial')->count(),
                    Sponsorship::where('sponsorship_type', 'educational')->count(),
                    Sponsorship::where('sponsorship_type', 'medical')->count(),
                ],
                'backgroundColor' => ['#3b82f6', '#f59e0b', '#10b981'],
            ]],
            'labels' => ['كفالة مالية', 'كفالة دراسية', 'كفالة علاجية'],
        ];
    }

    protected function getType(): string { return 'pie'; }
}