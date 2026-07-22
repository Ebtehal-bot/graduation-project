<?php

namespace App\Filament\Widgets;

use App\Models\Orphan;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class DashboardKpiCards extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return str_contains(request()->url(), 'analytics-dashboard');
    }

    protected function getCards(): array
    {
        $today = now();
        $nextMonth = now()->addDays(30);
        $totalOrphans = Orphan::count();
        $sponsored = Sponsorship::where('status', 'active')->distinct('orphan_id')->count('orphan_id');
        $coverageRate = $totalOrphans > 0 ? round(($sponsored / $totalOrphans) * 100) : 0;
        $totalPayments = Payment::whereYear('created_at', date('Y'))->sum('amount');
        $branches = \App\Models\Branch::withCount('orphans')->get();
        $totalBranches = $branches->count();
        $topBranch = $branches->sortByDesc('orphans_count')->first();

        return [
            Card::make('إجمالي الأيتام', number_format($totalOrphans))
                ->description('مسجل في النظام')
                ->color('primary')
                ->icon('heroicon-o-user-group'),

            Card::make('نسبة التغطية', "{$coverageRate}%")
                ->description("{$sponsored} مكفول من {$totalOrphans}")
                ->color($coverageRate > 60 ? 'success' : 'warning')
                ->icon('heroicon-o-shield-check'),

            Card::make('إجمالي المدفوعات السنوية', number_format($totalPayments) . ' ر.ي')
                ->description('منذ بداية العام')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),

            Card::make('عدد الفروع', "{$totalBranches} فرع")
                ->description($topBranch ? "الأكثر نشاطاً: {$topBranch->name}" : '')
                ->color('primary')
                ->icon('heroicon-o-office-building'),

            Card::make('كفالات ستنتهي قريباً',
                Sponsorship::where('status', 'active')
                    ->whereNotNull('end_date')
                    ->where('end_date', '<=', $nextMonth)
                    ->where('end_date', '>=', $today)
                    ->count() . ' كفالة'
            )
                ->description('خلال 30 يوماً قادمة')
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle'),

            Card::make('أيتام غير مكفولين',
                Orphan::whereDoesntHave('sponsorships', fn($q) => $q->where('status', 'active'))->count() . ' يتيم'
            )
                ->description('بحاجة لكفالة عاجلة')
                ->color('danger')
                ->icon('heroicon-o-heart'),
        ];
    }
}
