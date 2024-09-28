<?php

namespace App\Services;

use App\Models\User;
use App\Models\StockTrade;

class DarfService
{
    public function calculateDarfValuesByMonth(int $year, int $month): array
    {
        /** @var User $user */
        $user = auth()->user();

        $sells = $user->stockTrades()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('operation', StockTrade::OPERATION_SELL)
            ->orderBy('date')
            ->get();

        foreach ($sells as $sell) {
            $avgPrice = $user->stockTrades()
                ->where('stock_symbol', $sell->stock_symbol)
                ->where('operation', StockTrade::OPERATION_BUY)
                ->where('date', '<=', $sell->date)
                ->selectRaw('SUM(quantity * price) / SUM(quantity) as avg_price')
                ->first()->avg_price;

            $sell->avg_price = $avgPrice;
            $sell->is_day_trade = $sell->isDayTrade();
        }

    }

    public function getBrazilStocks(): array
    {
        $data = file_get_contents('https://www.b3.com.br/pt_br/market-data-e-indices/servicos-de-dados/market-data/cotacoes/cotacoes/');
    }
}
