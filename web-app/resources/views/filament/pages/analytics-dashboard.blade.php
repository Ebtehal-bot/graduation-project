<x-filament::page>
    @php
        $branchWidget = \App\Filament\Widgets\BranchPerformanceWidget::class;
        $paymentWidget = \App\Filament\Widgets\PaymentChart::class;
        $statusWidget = \App\Filament\Widgets\SponsorshipStatusChart::class;
        $typeWidget = \App\Filament\Widgets\SponsorshipTypeChart::class;
        $chartWidgets = [$statusWidget, $typeWidget, $branchWidget, $paymentWidget];
    @endphp

    {{-- المخططات البيانية --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($chartWidgets as $widget)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-4
                {{ $widget === $paymentWidget ? 'md:col-span-2' : '' }}
                {{ $widget === $branchWidget ? 'md:col-span-2' : '' }}">
                <div class="h-full">
                    @livewire($widget)
                </div>
            </div>
        @endforeach
    </div>
</x-filament::page>