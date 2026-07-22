<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    private array $settings = [
        // General Settings
        ['site_name', 'منصة كفيل لرعاية وكفالة الأيتام', 'general', 'string'],
        ['site_description', 'منصة متكاملة لإدارة الأيتام والكفلاء والكفالات ومتابعة التقارير والإشعارات', 'general', 'string'],
        ['site_email', 'info@orphans.org', 'general', 'string'],
        ['site_phone', '+967 xxx xxx xxx', 'general', 'string'],
        ['site_whatsapp', '+967 xxx xxx xxx', 'general', 'string'],
        ['site_address', 'اليمن', 'general', 'string'],

        // Organization Settings
        ['org_name', 'منصة كفيل', 'organization', 'string'],
        ['org_manager', '', 'organization', 'string'],
        ['org_email', '', 'organization', 'string'],
        ['org_phone', '', 'organization', 'string'],
        ['org_website', '', 'organization', 'string'],
        ['org_address', '', 'organization', 'string'],

        // Contact Settings
        ['contact_phone', '+967 xxx xxx xxx', 'contact', 'string'],
        ['contact_whatsapp', '+967 xxx xxx xxx', 'contact', 'string'],
        ['contact_email', 'info@orphans.org', 'contact', 'string'],
        ['contact_facebook', '', 'contact', 'string'],
        ['contact_twitter', '', 'contact', 'string'],
        ['contact_instagram', '', 'contact', 'string'],
        ['contact_youtube', '', 'contact', 'string'],

        // Notification Settings
        ['notifications_enabled', '1', 'notifications', 'boolean'],
        ['notifications_sponsorship', '1', 'notifications', 'boolean'],
        ['notifications_payment', '1', 'notifications', 'boolean'],
        ['notifications_orphan_updates', '1', 'notifications', 'boolean'],
        ['notifications_system', '1', 'notifications', 'boolean'],

        // Application Settings
        ['app_default_language', 'ar', 'application', 'string'],
        ['app_timezone', 'Asia/Aden', 'application', 'string'],
        ['app_date_format', 'Y-m-d', 'application', 'string'],
        ['app_currency', 'YER', 'application', 'string'],

        // About System
        ['about_system', 'منصة كفيل لرعاية وكفالة الأيتام هي منصة متكاملة لإدارة الأيتام والكفلاء والكفالات ومتابعة التقارير والإشعارات بطريقة احترافية وآمنة.', 'about', 'string'],

        // Security Settings
        ['security_session_timeout', '120', 'security', 'integer'],
        ['security_password_min_length', '8', 'security', 'integer'],
        ['security_password_uppercase', '1', 'security', 'boolean'],
        ['security_password_numbers', '1', 'security', 'boolean'],
        ['security_password_symbols', '0', 'security', 'boolean'],
        ['security_login_attempts', '5', 'security', 'integer'],
        ['security_two_factor', '0', 'security', 'boolean'],

        // Appearance Settings
        ['appearance_theme_mode', 'light', 'appearance', 'string'],
        ['appearance_primary_color', 'green', 'appearance', 'string'],
        ['appearance_logo_normal', '', 'appearance', 'string'],
        ['appearance_logo_dark', '', 'appearance', 'string'],

        // Backup Settings
        ['backup_auto_enabled', '0', 'backup', 'boolean'],
        ['backup_frequency', 'yearly', 'backup', 'string'],
        ['backup_status', 'لم يتم إنشاء نسخة احتياطية بعد', 'backup', 'string'],
        ['backup_next_date', '', 'backup', 'string'],
        ['backup_annual_enabled', '0', 'backup', 'boolean'],
        ['backup_annual_date', '', 'backup', 'string'],
        ['backup_retention_years', '5', 'backup', 'integer'],
        ['backup_storage_destination', 'local', 'backup', 'string'],
        ['backup_last_size', '', 'backup', 'string'],
        ['backup_retention_count', '30', 'backup', 'integer'],
        ['backup_retention_auto_delete', '0', 'backup', 'boolean'],
        ['backup_notify_success', '1', 'backup', 'boolean'],
        ['backup_notify_failure', '1', 'backup', 'boolean'],
        ['backup_external_disk_path', '', 'backup', 'string'],
    ];

    public function run()
    {
        foreach ($this->settings as $setting) {
            Setting::setValue($setting[0], $setting[1], $setting[2], $setting[3]);
        }
    }
}
