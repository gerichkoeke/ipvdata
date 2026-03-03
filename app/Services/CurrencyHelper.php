<?php

namespace App\Services;

class CurrencyHelper
{
    public static function format(float $value, string $currency = 'BRL'): string
    {
        return match($currency) {
            'USD' => 'US$ ' . number_format($value, 2, '.', ','),
            'PYG' => '₲ '   . number_format($value, 0, ',', '.'),
            default => 'R$ ' . number_format($value, 2, ',', '.'),
        };
    }

    public static function symbol(string $currency = 'BRL'): string
    {
        return match($currency) {
            'USD' => 'US$',
            'PYG' => '₲',
            default => 'R$',
        };
    }

    public static function decimals(string $currency = 'BRL'): int
    {
        return $currency === 'PYG' ? 0 : 2;
    }

    // Moeda do usuário logado
    public static function userCurrency(): string
    {
        $user = auth()->user();
        if (!$user) return 'BRL';
        return $user->active_currency ?? 'BRL';
    }

    public static function formatForUser(float $value): string
    {
        return self::format($value, self::userCurrency());
    }
}
