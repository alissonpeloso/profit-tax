<?php

namespace App\Enum;

enum StockTradeOperation: string
{
    case BUY = 'buy';
    case SELL = 'sell';
    case EXTRAORDINARY = 'extraordinary';

    public function getLabel(): string
    {
        return match ($this) {
            self::BUY => __('Buy'),
            self::SELL => __('Sell'),
            self::EXTRAORDINARY => __('Extraordinary Event'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BUY => 'success',
            self::SELL => 'danger',
            self::EXTRAORDINARY => 'warning',
        };
    }
}
