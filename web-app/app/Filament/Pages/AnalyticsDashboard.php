<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\SponsorshipStatusChart; 
use App\Filament\Widgets\SponsorshipTypeChart;   
use App\Filament\Widgets\PaymentChart;
use App\Filament\Widgets\BranchPerformanceWidget;
use App\Filament\Widgets\DashboardKpiCards;

class AnalyticsDashboard extends Page
{
    public static function canView(): bool
    {
        if ($user = auth()->user()) {
            return $user->hasRole('super_admin') || $user->hasRole('supervisor');
        }
        return false;
    }
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static string $view = 'filament.pages.analytics-dashboard';

    protected static ?string $slug = 'analytics-dashboard';

    public static function getNavigationLabel(): string
    {
        return __('sidebar.analytics_dashboard');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.nav_group.reports_analytics');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardKpiCards::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}