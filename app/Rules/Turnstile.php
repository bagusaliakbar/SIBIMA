<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.turnstile.secret_key');

        // Jika secret key belum diatur atau kosong, lewati validasi
        if (empty($secretKey)) {
            return;
        }

        // Token Turnstile harus berupa string non-kosong
        if (empty($value) || ! is_string($value)) {
            $fail('Mohon selesaikan verifikasi keamanan Cloudflare Turnstile.');
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (! $response->successful() || ! ($response->json('success') === true)) {
                Log::warning('Cloudflare Turnstile verification rejected', [
                    'ip' => request()->ip(),
                    'errors' => $response->json('error-codes', []),
                ]);

                $fail('Verifikasi keamanan Cloudflare Turnstile tidak valid atau telah kedaluwarsa. Silakan refresh halaman.');
            }
        } catch (\Throwable $e) {
            Log::error('Cloudflare Turnstile connection failed: ' . $e->getMessage());
            $fail('Gagal menghubungi server verifikasi Cloudflare. Silakan coba kembali.');
        }
    }
}
