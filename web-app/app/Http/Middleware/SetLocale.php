<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        } else {
            try {
                $locale = \App\Models\Setting::getValue('site_locale', 'ar');
            } catch (\Exception $e) {
                $locale = 'ar';
            }
        }

        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        app()->setLocale($locale);
        setlocale(LC_TIME, $locale === 'ar' ? 'ar_AE.utf8' : 'en_US.utf8');

        return $next($request);
    }
}
