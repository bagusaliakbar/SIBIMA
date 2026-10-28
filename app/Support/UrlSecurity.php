<?php

namespace App\Support;

class UrlSecurity
{
    /**
     * Validate that an external URL uses HTTP/HTTPS and does not resolve to
     * loopback, private, link-local, or reserved IP ranges (SSRF protection).
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

        // Resolve DNS records for the hostname
        $ips = @gethostbynamel($host);
        if (!is_array($ips) || empty($ips)) {
            // If DNS cannot resolve on the server, reject server-side fetch
            return false;
        }

        foreach ($ips as $ip) {
            if (!self::isPublicIp($ip)) {
                return false;
            }
        }

        return true;
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

