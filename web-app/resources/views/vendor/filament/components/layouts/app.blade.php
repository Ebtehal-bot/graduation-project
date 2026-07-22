@props([
    'maxContentWidth' => null,
])

<x-filament::layouts.base :title="$title">
    {{-- إضافة استايل الألوان الأكاديمية هنا لضمان التطبيق الفوري --}}
    <style>
        :root {
            /* درجات الكحلي الملكي بدلاً من البرتقالي */
            --primary-50: 238, 242, 255 !important;
            --primary-500: 30, 58, 138 !important; 
            --primary-600: 23, 37, 84 !important;
        }

        /* تغيير لون الخلفية للعنصر النشط في القائمة الجانبية */
        .filament-sidebar-item-active { 
    background-color: rgb(30, 58, 138) !important; 
    color: white !important;
    border-radius: 12px !important; /* حواف دائرية أكثر */
    margin: 4px 12px !important; /* إضافة مسافة من الجوانب */
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
        
        /* جعل أيقونة ونص العنصر المختار باللون الأبيض لتباين أفضل */
        .filament-sidebar-item-active .filament-sidebar-item-icon,
        .filament-sidebar-item-active span {
            color: white !important;
        }

        /* تحويل أي أزرار أو عناصر خلفية برتقالية إلى كحلي */
        .filament-button, .bg-primary-600, .bg-primary-500 {
            background-color: rgb(30, 58, 138) !important;
        }
    </style>

    <div class="filament-app-layout flex h-full w-full overflow-x-clip">
        <div
            x-data="{}"
            x-cloak
            x-show="$store.sidebar.isOpen"
            x-transition.opacity.500ms
            x-on:click="$store.sidebar.close()"
            class="filament-sidebar-close-overlay fixed inset-0 z-20 w-full h-full bg-gray-900/50 lg:hidden"
        ></div>

        <x-filament::layouts.app.sidebar />

        <div
            @if (config('filament.layout.sidebar.is_collapsible_on_desktop'))
                x-data="{}"
                x-bind:class="{
                    'lg:pl-[var(--collapsed-sidebar-width)] rtl:lg:pr-[var(--collapsed-sidebar-width)]': ! $store.sidebar.isOpen,
                    'filament-main-sidebar-open lg:pl-[var(--sidebar-width)] rtl:lg:pr-[var(--sidebar-width)]': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @endif
            @class([
                'filament-main flex-col gap-y-6 w-screen flex-1 rtl:lg:pl-0',
                'hidden h-full transition-all' => config('filament.layout.sidebar.is_collapsible_on_desktop'),
                'flex lg:pl-[var(--sidebar-width)] rtl:lg:pr-[var(--sidebar-width)]' => ! config('filament.layout.sidebar.is_collapsible_on_desktop'),
            ])
        >
            <x-filament::topbar :breadcrumbs="$breadcrumbs" />

            <div @class([
                'filament-main-content flex-1 w-full px-4 mx-auto md:px-6 lg:px-8',
                match ($maxContentWidth ??= config('filament.layout.max_content_width')) {
                    null, '7xl', '' => 'max-w-7xl',
                    'xl' => 'max-w-xl',
                    '2xl' => 'max-w-2xl',
                    '3xl' => 'max-w-3xl',
                    '4xl' => 'max-w-4xl',
                    '5xl' => 'max-w-5xl',
                    '6xl' => 'max-w-6xl',
                    'full' => 'max-w-full',
                    default => $maxContentWidth,
                },
            ])>
                {{ \Filament\Facades\Filament::renderHook('content.start') }}

                {{ $slot }}

                {{ \Filament\Facades\Filament::renderHook('content.end') }}
            </div>

            <div class="filament-main-footer py-4 shrink-0">
                <x-filament::footer />
            </div>
        </div>
    </div>
</x-filament::layouts.base>