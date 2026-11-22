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
     * Check if the Bug Report & Feature Request widget/feature is enabled globally.
     */
    public static function isBugReportEnabled(): bool
    {
        try {
            return (bool) self::get('bug_report_enabled', true);
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Set the global Bug Report & Feature Request feature status.
     */
    public static function setBugReportEnabled(bool $enabled): void
    {
        self::set('bug_report_enabled', $enabled, 'boolean');
    }

    /**
     * Check if WhatsApp circuit breaker is currently tripped.
     */
    public static function isWhatsAppCircuitTripped(): bool
    {
        return Cache::has('wa_circuit_breaker_tripped');
    }

    /**
     * Get the reason why WhatsApp circuit breaker was tripped.
     */
    public static function getWhatsAppCircuitReason(): ?string
    {
        return Cache::get('wa_circuit_breaker_reason');
    }

    /**
     * Reset the WhatsApp circuit breaker.
     */
    public static function resetWhatsAppCircuit(): void
    {
        Cache::forget('wa_circuit_breaker_tripped');
        Cache::forget('wa_circuit_breaker_reason');
    }

    /**
     * Get the current maintenance mode configuration with safe defaults.
     */
    public static function getMaintenanceConfig(): array
    {
        try {
            return [
                'enabled'       => (bool) self::get('maintenance_enabled', false),
                'mode'          => (string) self::get('maintenance_mode', 'banner'),
                'target'        => (string) self::get('maintenance_target', 'both'),
                'title'         => (string) self::get('maintenance_title', 'Pemeliharaan Sistem SIBIMA'),
                'message'       => (string) self::get('maintenance_message', 'Sistem sedang dalam pemeliharaan terjadwal untuk peningkatan layanan dan performa. Mohon maaf atas ketidaknyamanannya.'),
                'estimate'      => (string) self::get('maintenance_estimate', ''),
                'ends_at'       => (string) self::get('maintenance_ends_at', ''),
                'block_guest'   => (bool) self::get('maintenance_block_guest', false),
                'bypass_secret' => (string) self::get('maintenance_bypass_secret', ''),
            ];
        } catch (\Throwable $e) {
            return [
                'enabled'       => false,
                'mode'          => 'banner',
                'target'        => 'both',
                'title'         => 'Pemeliharaan Sistem SIBIMA',
                'message'       => 'Sistem sedang dalam pemeliharaan terjadwal untuk peningkatan layanan dan performa. Mohon maaf atas ketidaknyamanannya.',
                'estimate'      => '',
                'ends_at'       => '',
                'block_guest'   => false,
                'bypass_secret' => '',
            ];
        }
    }

    /**
     * Save the maintenance mode configuration.
     */
    public static function saveMaintenanceConfig(array $config): void
    {
        self::set('maintenance_enabled', (bool) ($config['enabled'] ?? false), 'boolean');
        self::set('maintenance_mode', (string) ($config['mode'] ?? 'banner'), 'string');
        self::set('maintenance_target', (string) ($config['target'] ?? 'both'), 'string');
        self::set('maintenance_title', (string) ($config['title'] ?? 'Pemeliharaan Sistem SIBIMA'), 'string');
        self::set('maintenance_message', (string) ($config['message'] ?? ''), 'string');
        self::set('maintenance_estimate', (string) ($config['estimate'] ?? ''), 'string');
        self::set('maintenance_ends_at', (string) ($config['ends_at'] ?? ''), 'string');
        self::set('maintenance_block_guest', (bool) ($config['block_guest'] ?? false), 'boolean');

        if (array_key_exists('bypass_secret', $config)) {
            self::set('maintenance_bypass_secret', trim((string) $config['bypass_secret']), 'string');
        }
    }

    /**
     * Ensure a non-empty secret bypass token exists for guest-locked maintenance mode.
     */
    public static function getOrCreateMaintenanceBypassSecret(): string
    {
        $existing = trim((string) self::get('maintenance_bypass_secret', ''));
        if ($existing !== '') {
            return $existing;
        }

        $generated = 'sibima-' . strtolower(\Illuminate\Support\Str::random(12));
        self::set('maintenance_bypass_secret', $generated, 'string');

        return $generated;
    }

    /**
     * Determine whether pre-login (guest) pages are locked behind the maintenance screen.
     */
    public static function isGuestMaintenanceLocked(?array $config = null): bool
    {
        $cfg = $config ?? self::getMaintenanceConfig();

        return !empty($cfg['enabled'])
            && ($cfg['mode'] ?? 'banner') === 'lock'
            && !empty($cfg['block_guest']);
    }

    /**
     * Resolve effective maintenance countdown target timestamp (ISO-8601).
     * Uses explicit `ends_at` first, or deterministically parses an explicit wall-clock
     * time (e.g. "Sampai 12.00" or "23:00 WIB") from `estimate` as today's target time.
     */
    public static function resolveMaintenanceEndsAt(?array $config = null): ?string
    {
        $cfg = $config ?? self::getMaintenanceConfig();
        $tz = config('app.timezone', 'Asia/Jakarta');

        if (!empty($cfg['ends_at'])) {
            try {
                return \Carbon\Carbon::parse($cfg['ends_at'], $tz)->timezone($tz)->toIso8601String();
            } catch (\Throwable $e) {
                // Fall through to estimate parsing
            }
        }

        $estimate = trim((string) ($cfg['estimate'] ?? ''));
        if ($estimate !== '' && preg_match('/\b([01]?\d|2[0-3])[:.]([0-5]\d)\b/', $estimate, $matches)) {
            $hour = (int) $matches[1];
            $minute = (int) $matches[2];

            return \Carbon\Carbon::now($tz)
                ->setTime($hour, $minute, 0)
                ->toIso8601String();
        }

        return null;
    }

    /**
     * Determine whether maintenance mode applies to a given user role.
     * Admin and Kaprodi are never locked out or targeted.
     */
    public static function isMaintenanceActiveForRole(?string $role, ?array $config = null): bool
    {
        if (!in_array($role, ['mahasiswa', 'dosen'], true)) {
            return false;
        }

        $cfg = $config ?? self::getMaintenanceConfig();
        if (empty($cfg['enabled'])) {
            return false;
        }

        return $cfg['target'] === 'both' || $cfg['target'] === $role;
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
