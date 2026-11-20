<?php

namespace App\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UrlSecurity
{
    /**
     * Validate that an external URL uses HTTP/HTTPS and does not resolve to
     * loopback, private, link-local, or reserved IPv4/IPv6 ranges (SSRF protection).
     */
    public static function isSafeExternalUrl(?string $url): bool
    {
        if (empty($url) || !is_string($url)) {
            return false;
        }

        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parsed = parse_url($url);
        if (!is_array($parsed)) {
            return false;
        }

        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower(trim($parsed['host'] ?? '', '[]'));
        if ($host === '') {
            return false;
        }

        // Block localhost and internal host suffixes immediately
        $blockedHosts = ['localhost', 'localhost.localdomain', 'ip6-localhost', 'ip6-loopback', 'metadata.google.internal'];
        if (in_array($host, $blockedHosts, true) || str_ends_with($host, '.local') || str_ends_with($host, '.internal') || str_ends_with($host, '.test')) {
            return false;
        }

        // If the host is an IP literal (IPv4 or IPv6), validate directly
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return self::isPublicIp($host);
        }

        // Resolve BOTH IPv4 (A) and IPv6 (AAAA) DNS records to prevent IPv6 SSRF bypass
        $ips = [];
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if (is_array($records)) {
            foreach ($records as $rec) {
                if (!empty($rec['ip'])) {
                    $ips[] = $rec['ip'];
                }
                if (!empty($rec['ipv6'])) {
                    $ips[] = $rec['ipv6'];
                }
            }
        }

        // Fallback to IPv4 lookup if dns_get_record is restricted
        if (empty($ips)) {
            $fallbackIps = @gethostbynamel($host);
            if (is_array($fallbackIps)) {
                $ips = $fallbackIps;
            }
        }

        if (empty($ips)) {
            return false;
        }

        foreach (array_unique($ips) as $ip) {
            if (!self::isPublicIp($ip)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Perform an SSRF-safe HTTP GET request that validates EVERY redirect hop
     * against isSafeExternalUrl() and enforces TLS verification in production.
     */
    public static function safeHttpGet(string $url, array $headers = [], int $timeout = 10, int $maxRedirects = 3): ?Response
    {
        $currentUrl = $url;
        $verifyTls = !app()->environment('local');

        for ($hop = 0; $hop <= $maxRedirects; $hop++) {
            if (!self::isSafeExternalUrl($currentUrl)) {
                return null;
            }

            $client = Http::withHeaders($headers)
                ->withOptions([
                    'allow_redirects' => false,
                    'verify' => $verifyTls,
                ])
                ->timeout($timeout);

            $response = $client->get($currentUrl);
            $status = $response->status();

            if (in_array($status, [301, 302, 303, 307, 308], true)) {
                $location = $response->header('Location');
                if (empty($location)) {
                    return $response;
                }

                // Resolve relative redirect URLs against current URL
                if (!preg_match('#^https?://#i', $location)) {
                    $parsed = parse_url($currentUrl);
                    $origin = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '') . (isset($parsed['port']) ? ':' . $parsed['port'] : '');
                    $location = str_starts_with($location, '/')
                        ? $origin . $location
                        : $origin . '/' . ltrim($location, '/');
                }

                $currentUrl = $location;
                continue;
            }

            return $response;
        }

        return null;
    }

    /**
     * Validate that a URL syntax and host are safe for storing in user forms
     * (does not require live DNS lookup during form validation to avoid latency,
     * while blocking loopback/private IP literals and local domains).
     */
    public static function isSafeUserSubmittedUrl(?string $url): bool
    {
        if (empty($url) || !is_string($url)) {
            return false;
        }

        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parsed = parse_url($url);
        if (!is_array($parsed)) {
            return false;
        }

        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower(trim($parsed['host'] ?? '', '[]'));
        if ($host === '') {
            return false;
        }

        $blockedHosts = ['localhost', 'localhost.localdomain', 'ip6-localhost', 'ip6-loopback', 'metadata.google.internal'];
        if (in_array($host, $blockedHosts, true) || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return self::isPublicIp($host);
        }

        return true;
    }

    /**
     * Check if an IP address is strictly public (not private, loopback, link-local, or reserved).
     */
    public static function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }
}
