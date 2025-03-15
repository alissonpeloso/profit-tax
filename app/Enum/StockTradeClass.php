<?php

namespace App\Enum;

enum StockTradeClass: string
{
    case STOCK = 'stock';
    case BDR = 'bdr';
    case ETF = 'etf';
    case FII = 'fii';

    public function getLabel(): string
    {
        return match ($this) {
            self::STOCK => __('Stock'),
            self::BDR => __('BDR'),
            self::ETF => __('ETF'),
            self::FII => __('FII'),
        };
    }
}
