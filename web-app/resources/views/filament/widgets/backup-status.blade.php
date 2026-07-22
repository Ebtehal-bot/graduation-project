<x-filament::widget>
    <x-filament::card>
        <h2 class="text-xl font-bold mb-4 text-center" style="color: rgb(46, 125, 50);">
            حالة النسخ الاحتياطي
        </h2>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">حالة النسخ الاحتياطي:</span>
                <span class="font-semibold">{{ $status }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">آخر تاريخ للنسخ الاحتياطي:</span>
                <span>{{ $lastBackup }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">تاريخ النسخ الاحتياطي القادم:</span>
                <span>{{ $nextBackup }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">وجهة التخزين:</span>
                <span>{{ $storage }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">حجم النسخة الاحتياطية:</span>
                <span>{{ $size }}</span>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
