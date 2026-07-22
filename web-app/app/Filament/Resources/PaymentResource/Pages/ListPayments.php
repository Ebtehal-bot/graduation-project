<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    // 1. دالة الأزرار (مثل زر إضافة دفعة جديدة)
    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // 2. دالة عرض الإحصائيات (Widgets) فوق الجدول مباشرة
    protected function getHeaderWidgets(): array
    {
        return [
            // استدعاء الويجت الذي يعرض مبالغ السنة
            \App\Filament\Resources\PaymentResource\Widgets\PaymentStats::class,
        ];
    }
}