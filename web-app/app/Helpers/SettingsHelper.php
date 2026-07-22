<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    private static array $cache = [];

    public static function get(string $key, $default = null)
    {
        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = Setting::getValue($key);
        }
        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, $value, string $group = 'general', string $type = 'string'): void
    {
        Setting::setValue($key, $value, $group, $type);
        self::$cache[$key] = $value;
    }

    public static function getGroup(string $group): array
    {
        return Setting::getGroup($group);
    }

    public static function all(): array
    {
        if (empty(self::$cache)) {
            $settings = Setting::all();
            foreach ($settings as $setting) {
                self::$cache[$setting->key] = match ($setting->type) {
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $setting->value,
                    'json' => json_decode($setting->value, true) ?? [],
                    default => $setting->value,
                };
            }
        }
        return self::$cache;
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
