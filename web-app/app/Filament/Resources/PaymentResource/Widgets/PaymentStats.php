<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use App\Models\Sponsorship;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PaymentStats extends BaseWidget
{
    protected $listeners = ['updatePaymentStats' => '$refresh'];

    protected function getCards(): array
    {
        $filters = request()->query('tableFilters');
        $year = $filters['year_report']['year'] ?? null;

        // 1. حساب المستهدف السنوي العام (إجمالي المبالغ الشهرية من جدول الكفالات × 12)
        $expectedYearly = Sponsorship::sum('monthly_amount') * 12;

        // 2. تجهيز استعلام المبالغ المحصلة فعلياً
        $query = Payment::where('payment_status', 'paid');

        if ($year) {
            $query->whereYear('date', $year);
            $title = "إجمالي تحصيل سنة $year";
            $desc = "المبالغ المستلمة فعلياً في " . $year;
        } else {
            $title = "إجمالي التحصيل الفعلي";
            $desc = "مجموع كل السندات المدفوعة";
        }

        $totalPaid = (clone $query)->sum('amount');
        $count = (clone $query)->count();

        return [
            Card::make($title, number_format($totalPaid) . ' ر.ي')
                ->description($desc)
                ->descriptionIcon('heroicon-s-cash')
                ->color('success'),

            Card::make("المستهدف السنوي العام", number_format($expectedYearly) . ' ر.ي')
                ->description('إجمالي الكفالات السنوية المفترضة')
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('secondary'),
            
            Card::make("عدد السندات", $count)
                ->description('إجمالي عمليات الدفع المسجلة')
                ->descriptionIcon('heroicon-s-collection')
                ->color('primary'),
        ];
    }
}