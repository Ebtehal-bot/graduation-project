<?php

namespace App\Filament\Widgets;

use App\Models\Payment; // تأكدي أن اسم الموديل الخاص بالمدفوعات هو Payment
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentChart extends ChartWidget
{
    public static function canView(): bool
    {
        return str_contains(request()->url(), 'analytics-dashboard');
    }

    protected static ?string $heading = 'إحصائيات المدفوعات الشهرية (السنة الحالية)';

    protected function getData(): array
    {
        // جلب مجموع المدفوعات لكل شهر في السنة الحالية
        $payments = DB::table('payments')
            ->select(DB::raw('SUM(amount) as total'), DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        // تجهيز البيانات لـ 12 شهر لضمان ظهور الرسم كاملاً
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $payments[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'إجمالي المبالغ',
                    'data' => $data,
                    'backgroundColor' => '#10b981', // لون أخضر مالي
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
        ];
    }

    protected function getType(): string
    {
        return 'line'; // الرسم الخطي أفضل للمدفوعات لمراقبة الصعود والهبوط
    }
}