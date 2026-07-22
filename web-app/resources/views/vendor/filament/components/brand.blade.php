@php
    $logo = \App\Models\Setting::getValue('site_logo', '');
    $logoDark = \App\Models\Setting::getValue('appearance_logo_dark', '');
    $brandName = config('filament.brand', 'منصة كفيل لرعاية وكفالة الأيتام');
    $isDark = config('filament.dark_mode');
@endphp

@if (filled($brandName))
    <div @class([
        'filament-brand text-xl font-bold tracking-tight flex items-center gap-3',
        'dark:text-white' => $isDark,
    ])>
        @if (!empty($logo))
            <img src="{{ asset('storage/' . $logo) }}" alt="{{ $brandName }}"
                 class="h-10 w-auto object-contain">
        @elseif (!empty($logoDark) && $isDark)
            <img src="{{ asset('storage/' . $logoDark) }}" alt="{{ $brandName }}"
                 class="h-10 w-auto object-contain">
        @else
            {{ $brandName }}
        @endif
    </div>
@endif
