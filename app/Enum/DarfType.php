<?php

namespace App\Enum;

enum DarfType: string
{
    case DAY_TRADE = 'day_trade';
    case SWING_TRADE = 'swing_trade';

    public function getLabel(): string
    {
        return match ($this) {
            self::DAY_TRADE => __('Day Trade'),
            self::SWING_TRADE => __('Swing Trade'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DAY_TRADE => 'warning',
            self::SWING_TRADE => 'positive',
        };
    }
}
