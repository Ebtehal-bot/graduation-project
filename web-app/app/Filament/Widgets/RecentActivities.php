<?php

namespace App\Filament\Widgets;

use App\Models\Sponsorship;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentActivities extends BaseWidget
{
    /**
     * دالة التحكم في الظهور:
     * سنقوم بإرجاع true فقط إذا كان الرابط لا يحتوي على كلمة الإحصائيات.
     * هذا سيجعله يظهر في لوحة التحكم (Dashboard) ويختفي من صفحة التقارير.
     */
    public static function canView(): bool
    {
        return !str_contains(request()->url(), 'altkaryroalmkhttatalbyanyalthlyly');
    }

    protected static ?int $sort = 2;
    protected static ?string $heading = 'النشاطات الأخيرة';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Sponsorship::query()->with(['orphan', 'sponsor'])->latest()->limit(5); 
    }

    protected function getTableColumns(): array
    {
        return [
            // الوقت (منذ ساعة)
            Tables\Columns\TextColumn::make('created_at')
                ->label('')
                ->formatStateUsing(fn ($state) => $state->diffForHumans())
                ->extraAttributes(['class' => 'w-32 text-gray-400 text-sm bg-gray-50 rounded-md px-2']),

            // وصف النشاط
            Tables\Columns\TextColumn::make('activity')
                ->label('')
                ->getStateUsing(function ($record) {
                    return "تم تسجيل كفالة جديدة لليتيم: " . ($record->orphan->name ?? '---');
                })
                ->description(fn ($record) => "بواسطة الكفيل: " . ($record->sponsor->name ?? '---'))
                ->weight('bold'),
        ];
    }

    protected function isTablePaginationEnabled(): bool { return false; }
}