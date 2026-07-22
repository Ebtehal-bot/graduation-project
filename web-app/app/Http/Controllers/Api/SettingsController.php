<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $logo = Setting::getValue('site_logo', '');
        $logoDark = Setting::getValue('appearance_logo_dark', '');

        return response()->json([
            'status' => true,
            'data' => [
                'site_name' => Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام'),
                'org_name' => Setting::getValue('org_name', 'منصة كفيل'),
                'site_logo' => $logo ? asset('storage/' . $logo) : null,
                'logo_dark' => $logoDark ? asset('storage/' . $logoDark) : null,
                'site_email' => Setting::getValue('site_email', ''),
                'site_phone' => Setting::getValue('site_phone', ''),
                'app_currency' => Setting::getValue('app_currency', 'YER'),
                'site_whatsapp' => Setting::getValue('site_whatsapp', ''),
            ]
        ]);
    }
}
