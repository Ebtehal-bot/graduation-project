<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group_name',
        'type',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public function scopeGroup($query, $group)
    {
        return $query->where('group_name', $group);
    }

    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true) ?? $default,
            default => $setting->value,
        };
    }

    public static function setValue(string $key, $value, string $group = 'general', string $type = 'string'): void
    {
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'group_name' => $group, 'type' => $type]
        );
    }

    public static function getGroup(string $group): array
    {
        return static::where('group_name', $group)
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->key => match ($item->type) {
                    'boolean' => filter_var($item->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $item->value,
                    'json' => json_decode($item->value, true) ?? [],
                    default => $item->value,
                },
            ])
            ->toArray();
    }
}
