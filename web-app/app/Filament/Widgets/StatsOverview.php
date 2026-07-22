<?php

namespace App\Filament\Widgets;

use App\Models\Orphan;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

   protected function getCards(): array
{
    // التاريخ الحالي للمقارنة
    $today = now();
    $nextMonth = now()->addDays(30);

    return [
        Card::make('عدد الأيتام', \App\Models\Orphan::count()),
        Card::make(' عدد الكفلاء', \App\Models\Sponsor::count()),
        
        // الكفالات النشطة (تأكدي أنها تستخدم 'active')
        Card::make('الكفالات النشطة', Sponsorship::where('status', 'active')->count())
            ->color('success'),

        // الكفالات المنتهية (تأكدي أنها تستخدم 'stopped')
        Card::make('الكفالات المنتهية', Sponsorship::where('status', 'stopped')->count())
            ->color('danger'),

        // تنبيه: الكفالات النشطة التي سينتهي تاريخها خلال 30 يوم
        Card::make('كفالات ستنتهي قريباً', 
            Sponsorship::where('status', 'active')
                ->whereNotNull('end_date') // نتأكد أن التاريخ ليس فارغاً
                ->where('end_date', '<=', $nextMonth)
                ->where('end_date', '>=', $today)
                ->count() . ' كفالة'
        )
        ->description('يرجى المتابعة مع الكفلاء للتجديد')
        ->color('warning')
        ->extraAttributes([
            'class' => 'col-span-full shadow-sm border-r-4 border-orange-500 bg-orange-50/50 py-4',
        ]),

        // تنبيه: أيتام بدون كفالات
        Card::make('أيتام غير مكفولين', \App\Models\Orphan::whereDoesntHave('sponsorships')->count() . ' يتيم')
            ->description('يحتاجون لكفالة عاجلة')
            ->color('danger')
            ->extraAttributes([
                'class' => 'col-span-full shadow-sm border-r-4 border-red-500 bg-red-50/50 py-4',
            ]),
    ];
}
}