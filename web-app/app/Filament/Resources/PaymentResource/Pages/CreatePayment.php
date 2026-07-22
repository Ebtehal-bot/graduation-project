<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Notifications\Notification; // أضفنا هذا السطر هنا
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    // هذه الدالة تعمل تلقائياً فور نجاح عملية الحفظ
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('تم تسجيل دفعة جديدة بنجاح')
            ->icon('heroicon-o-cash')
            ->success() // لإظهار اللون الأخضر
            ->sendToDatabase(auth()->user()) // يرسلها لقاعدة البيانات
            ->send(); // يظهرها فوراً على الشاشة (Pop-up)
    }

    // لتوجيه المستخدم لجدول الدفعات بعد الإضافة بدلاً من البقاء في الصفحة
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}