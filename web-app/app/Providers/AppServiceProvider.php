<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class AppServiceProvider extends ServiceProvider
{
    private array $colorPalettes = [
        'default' => [
            '50' => '239, 246, 255',
            '100' => '219, 234, 254',
            '200' => '191, 219, 254',
            '300' => '147, 197, 253',
            '400' => '96, 165, 250',
            '500' => '59, 130, 246',
            '600' => '37, 99, 235',
            '700' => '29, 78, 216',
            '800' => '30, 64, 175',
            '900' => '30, 58, 138',
        ],
        'blue' => [
            '50' => '239, 246, 255',
            '100' => '219, 234, 254',
            '200' => '191, 219, 254',
            '300' => '147, 197, 253',
            '400' => '96, 165, 250',
            '500' => '59, 130, 246',
            '600' => '37, 99, 235',
            '700' => '29, 78, 216',
            '800' => '30, 64, 175',
            '900' => '30, 58, 138',
        ],
        'green' => [
            '50' => '240, 253, 244',
            '100' => '220, 252, 231',
            '200' => '187, 247, 208',
            '300' => '134, 239, 172',
            '400' => '74, 222, 128',
            '500' => '46, 125, 50',
            '600' => '39, 105, 42',
            '700' => '21, 128, 61',
            '800' => '22, 101, 52',
            '900' => '20, 83, 45',
        ],
        'emerald' => [
            '50' => '236, 253, 245',
            '100' => '209, 250, 229',
            '200' => '167, 243, 208',
            '300' => '110, 231, 183',
            '400' => '52, 211, 153',
            '500' => '16, 185, 129',
            '600' => '5, 150, 105',
            '700' => '4, 120, 87',
            '800' => '6, 95, 70',
            '900' => '6, 78, 59',
        ],
        'red' => [
            '50' => '254, 242, 242',
            '100' => '254, 226, 226',
            '200' => '254, 202, 202',
            '300' => '252, 165, 165',
            '400' => '248, 113, 113',
            '500' => '239, 68, 68',
            '600' => '220, 38, 38',
            '700' => '185, 28, 28',
            '800' => '153, 27, 27',
            '900' => '127, 29, 29',
        ],
        'orange' => [
            '50' => '255, 247, 237',
            '100' => '255, 237, 213',
            '200' => '254, 215, 170',
            '300' => '253, 186, 116',
            '400' => '251, 146, 60',
            '500' => '249, 115, 22',
            '600' => '234, 88, 12',
            '700' => '194, 65, 12',
            '800' => '154, 52, 18',
            '900' => '124, 45, 18',
        ],
        'purple' => [
            '50' => '250, 245, 255',
            '100' => '243, 232, 255',
            '200' => '233, 213, 255',
            '300' => '216, 180, 254',
            '400' => '192, 132, 252',
            '500' => '168, 85, 247',
            '600' => '147, 51, 234',
            '700' => '126, 34, 206',
            '800' => '107, 33, 168',
            '900' => '88, 28, 135',
        ],
        'gray' => [
            '50' => '249, 250, 251',
            '100' => '243, 244, 246',
            '200' => '229, 231, 235',
            '300' => '209, 213, 219',
            '400' => '156, 163, 175',
            '500' => '107, 114, 128',
            '600' => '75, 85, 99',
            '700' => '55, 65, 81',
            '800' => '31, 41, 55',
            '900' => '17, 24, 39',
        ],
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $locale = \App\Models\Setting::getValue('site_locale', 'ar');
                if (in_array($locale, ['ar', 'en'])) {
                    app()->setLocale($locale);
                }

                $themeMode = \App\Models\Setting::getValue('appearance_theme_mode', 'light');
                if ($themeMode === 'dark' || $themeMode === 'system') {
                    config(['filament.dark_mode' => true]);
                }

                $siteName = \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
                config(['filament.brand' => $siteName]);

                $favicon = \App\Models\Setting::getValue('site_favicon', '');
                if (!empty($favicon)) {
                    config(['filament.favicon' => asset('storage/' . $favicon)]);
                }
            }
        } catch (\Exception $e) {
            //
        }

        Filament::serving(function () {
            try {
                if (Schema::hasTable('settings')) {
                    $this->applyAppearanceSettings();
                }
            } catch (\Exception $e) {
                //
            }

            Filament::registerNavigationGroups([
                __('sidebar.nav_group.financial_management'),
                __('sidebar.nav_group.general_management'),
                __('sidebar.nav_group.reports'),
                __('sidebar.nav_group.reports_analytics'),
                __('sidebar.nav_group.system_management'),
            ]);

            Filament::registerRenderHook('global-search.start', function () {
                $locale = app()->getLocale();
                $target = $locale === 'ar' ? 'en' : 'ar';
                $label = $locale === 'ar' ? 'English 🌐' : 'العربية 🌐';

                return new HtmlString('
                    <a href="' . url('/lang/' . $target) . '"
                       class="inline-flex items-center gap-1 px-3 py-1.5 mx-2 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                       style="text-decoration:none;">
                        ' . $label . '
                    </a>
                ');
            });
        });
    }

    private function applyAppearanceSettings(): void
    {
        $primaryColor = \App\Models\Setting::getValue('appearance_primary_color', 'green');
        $themeMode = \App\Models\Setting::getValue('appearance_theme_mode', 'light');

        $palette = $this->colorPalettes[$primaryColor] ?? $this->colorPalettes['green'];
        $color500 = $palette['500'];
        $color600 = $palette['600'];

        $cssVars = '';
        foreach ($palette as $shade => $rgb) {
            $cssVars .= "                            --primary-{$shade}: {$rgb} !important;\n";
        }

        Filament::registerRenderHook(
            'styles.end',
            fn (): HtmlString => new HtmlString("
                    <style>
                        :root, html, body {
                {$cssVars}
                        }

                        html body .filament-brand,
                        html body .filament-brand span,
                        html body .filament-brand * {
                            color: rgb({$color500}) !important;
                        }

                        html body .filament-sidebar-nav .bg-primary-500,
                        html body .filament-sidebar-nav a.bg-primary-500,
                        html body .filament-sidebar-nav [class*=\"bg-primary-500\"],
                        html body .filament-sidebar-nav .bg-primary-600,
                        html body .filament-sidebar-nav a.bg-primary-600,
                        html body .filament-sidebar-item-active,
                        html body [class*=\"filament-sidebar-item-active\"] {
                            background-color: rgb({$color500}) !important;
                            background: rgb({$color500}) !important;
                            color: #ffffff !important;
                        }

                        html body .filament-sidebar-nav .bg-primary-500 *,
                        html body .filament-sidebar-nav a.bg-primary-500 *,
                        html body .filament-sidebar-nav .bg-primary-600 *,
                        html body .filament-sidebar-item-active *,
                        html body [class*=\"filament-sidebar-item-active\"] * {
                            color: #ffffff !important;
                            fill: #ffffff !important;
                        }

                        html body .filament-button,
                        html body button.filament-button,
                        html body a.filament-button,
                        html body .filament-page-actions a,
                        html body .filament-page-actions button,
                        html body .filament-page-actions button span,
                        html body .filament-button span,
                        html body .bg-primary-600 {
                            background-color: rgb({$color500}) !important;
                            border-color: rgb({$color500}) !important;
                            color: #ffffff !important;
                        }

                        html body .filament-button:hover,
                        html body button.filament-button:hover,
                        html body a.filament-button:hover,
                        html body .bg-primary-600:hover {
                            background-color: rgb({$color600}) !important;
                            border-color: rgb({$color600}) !important;
                        }
                    </style>
                "),
        );

        if ($themeMode === 'system') {
            Filament::registerRenderHook(
                'scripts.end',
                fn (): HtmlString => new HtmlString('
                    <script>
                        (function() {
                            var mq = window.matchMedia("(prefers-color-scheme: dark)");
                            function applyTheme(e) {
                                if (e.matches) {
                                    document.documentElement.classList.add("dark");
                                    localStorage.setItem("theme", "dark");
                                } else {
                                    document.documentElement.classList.remove("dark");
                                    localStorage.setItem("theme", "light");
                                }
                            }
                            applyTheme(mq);
                            mq.addEventListener("change", applyTheme);
                        })();
                    </script>
                '),
            );
        } elseif ($themeMode === 'dark') {
            Filament::registerRenderHook(
                'scripts.end',
                fn (): HtmlString => new HtmlString('
                    <script>
                        document.documentElement.classList.add("dark");
                        localStorage.setItem("theme", "dark");
                    </script>
                '),
            );
        } elseif ($themeMode === 'light') {
            Filament::registerRenderHook(
                'scripts.end',
                fn (): HtmlString => new HtmlString('
                    <script>
                        document.documentElement.classList.remove("dark");
                        localStorage.setItem("theme", "light");
                    </script>
                '),
            );
        }
    }
}
