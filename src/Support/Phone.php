<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Normalisation des numéros au format international E.164 (ex. +243812345678).
 */
final class Phone
{
    public static function normalize(string $raw, string $countryCode = '+243'): string
    {
        $digits = preg_replace('/[^\d+]/', '', $raw) ?? '';

        if (str_starts_with($digits, '+')) {
            return '+' . preg_replace('/\D/', '', $digits);
        }
        if (str_starts_with($digits, '00')) {
            return '+' . substr($digits, 2);
        }

        $code = ltrim($countryCode, '+');
        if (str_starts_with($digits, $code) && strlen($digits) > 10) {
            return '+' . $digits;
        }

        return $countryCode . ltrim($digits, '0');
    }

    /** Lien WhatsApp « click to chat » avec message pré-rempli. */
    public static function whatsappLink(string $phone, string $message = ''): string
    {
        $number = preg_replace('/\D/', '', $phone);

        return 'https://wa.me/' . $number . ($message !== '' ? '?text=' . rawurlencode($message) : '');
    }
}
