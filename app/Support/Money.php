<?php

namespace App\Support;

class Money
{
    public static function toMinor(null|int|float|string $amount): int
    {
        $normalized = str_replace([',', ' '], ['.', ''], (string) $amount);

        return (int) round(((float) $normalized) * 100);
    }

    public static function fromMinor(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
    }

    public static function formatMinor(int $amount, string $currency = 'EUR'): string
    {
        $symbols = [
            'EUR' => 'EUR',
            'USD' => 'USD',
            'GBP' => 'GBP',
        ];

        $currency = strtoupper($currency);

        return sprintf('%s %s', $symbols[$currency] ?? $currency, number_format($amount / 100, 2, '.', ','));
    }
}
