<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Format a phone number to standard Indonesian format (62...)
     * Fonnte expects numbers to usually start with country code without '+'.
     *
     * @param string|null $phoneNumber
     * @return string|null
     */
    public static function formatForWhatsApp(?string $phoneNumber): ?string
    {
        if (empty($phoneNumber)) {
            return null;
        }

        // Remove any non-numeric characters (like +, -, spaces, parentheses)
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If it starts with '08', replace with '628'
        if (str_starts_with($number, '08')) {
            $number = '628' . substr($number, 2);
        }

        // If it starts with '8' (and the user forgot the 0 or 62), prepend '62'
        if (str_starts_with($number, '8')) {
            $number = '62' . $number;
        }

        return $number;
    }
}
