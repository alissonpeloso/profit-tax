<?php

namespace App\Enum;

enum StockTradeOperation: string
{
    case BUY = 'buy';
    case SELL = 'sell';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUY => __('Buy'),
            self::SELL => __('Sell'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BUY => 'success',
            self::SELL => 'danger',
        };
    }
}
