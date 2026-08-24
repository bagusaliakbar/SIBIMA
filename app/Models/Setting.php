<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Retrieve a setting value by key with caching.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("app_setting_{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }
            return self::castValue($setting->value, $setting->type ?? 'string');
        });
    }

    /**
     * Set a setting value and invalidate the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public static function set(string $key, $value, string $type = 'string'): void
    {
        $serialized = is_bool($value) ? ($value ? '1' : '0') : (string)$value;
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $serialized, 'type' => $type]
        );
        Cache::forget("app_setting_{$key}");
    }

    /**
     * Check if WhatsApp notifications are enabled globally.
     *
     * @return bool
     */
    public static function isWhatsAppEnabled(): bool
    {
        return (bool) self::get('whatsapp_enabled', true);
    }

    /**
     * Set the global WhatsApp notification status.
     *
     * @param bool $enabled
     * @return void
     */
    public static function setWhatsAppEnabled(bool $enabled): void
    {
        self::set('whatsapp_enabled', $enabled, 'boolean');
    }

    /**
     * Cast string value to its respective type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }
}
