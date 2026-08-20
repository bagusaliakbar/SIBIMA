<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WaTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'content',
        'available_variables',
        'is_customized',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_customized' => 'boolean',
    ];

    /**
     * Clear cache for a specific template or all templates.
     */
    public static function clearCache(?string $code = null): void
    {
        if ($code) {
            Cache::forget("wa_template_{$code}");
        } else {
            $templates = self::all();
            foreach ($templates as $template) {
                Cache::forget("wa_template_{$template->code}");
            }
        }
    }

    /**
     * Parse template by replacing placeholders with actual data.
     *
     * @param string $code
     * @param array $data
     * @param string|null $defaultFallback
     * @return string
     */
    public static function parse(string $code, array $data = [], ?string $defaultFallback = null): string
    {
        $templateText = Cache::remember("wa_template_{$code}", 86400, function () use ($code, $defaultFallback) {
            $tpl = self::where('code', $code)->first();
            return $tpl ? $tpl->content : $defaultFallback;
        });

        if (empty($templateText)) {
            $templateText = $defaultFallback ?? '';
        }

        // Always ensure {link_login} or {link_dashboard} can be parsed if not explicitly provided
        if (!isset($data['link_login'])) {
            $data['link_login'] = url('/login');
        }
        if (!isset($data['link_dashboard'])) {
            $data['link_dashboard'] = url('/dashboard');
        }

        // Replace placeholders {variable_name}
        foreach ($data as $key => $value) {
            if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $templateText = str_replace('{' . $key . '}', (string) $value, $templateText);
            }
        }

        // Support Spintax resolution if used in templates, e.g. {Halo|Hai|Yth.|Salam}
        $templateText = preg_replace_callback('/\{([a-zA-Z0-9_\s\.\,\!\?\-]+\|[a-zA-Z0-9_\s\.\,\!\?\-\|]+)\}/', function ($matches) {
            $options = explode('|', $matches[1]);
            return trim($options[array_rand($options)]);
        }, $templateText);

        return $templateText;
    }
}
