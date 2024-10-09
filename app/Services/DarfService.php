<?php

namespace App\Services;

use App\Models\User;
use App\Models\StockTrade;
use Illuminate\Database\Eloquent\Collection;

class DarfService
{
    const int EXEMPTION_BR_STOCK_SELL_LIMIT = 20000;
    const int PERCENTAGE_BR_BDR_ETF_STOCK_IR = 15;
    const int PERCENTAGE_FII_IR = 20;
    const int PERCENTAGE_DAY_TRADE_IR = 20;

    public function calculateDarfValues(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $darfs = [];
        $this->calculateBRStocksDarf($user, $darfs);
        $this->calculateFIIDarf($user, $darfs);
        $this->calculateBDRAndETFStocksDarf($user, $darfs);
        $this->calculateDayTradeDarf($user, $darfs);

        return $darfs;
    }

    protected function calculateBRStocksDarf(User $user, array $darfs): void
    {
        $stockTrades = $user->stockTrades()->where([
            'class' => StockTrade::CLASS_STOCK,
            'is_day_trade' => false,
            'is_exempt' => false,
        ])->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_BR_BDR_ETF_STOCK_IR, self::EXEMPTION_BR_STOCK_SELL_LIMIT);
    }

    protected function calculateFIIDarf(User $user, array $darfs): void
    {
        $stockTrades = $user->stockTrades()->where([
            'class' => StockTrade::CLASS_FII,
            'is_day_trade' => false,
            'is_exempt' => false,
        ])->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_FII_IR);
    }

    protected function calculateBDRAndETFStocksDarf(User $user, array $darfs): void
    {
        $stockTrades = $user->stockTrades()->where(function ($query) {
            $query->where('class', StockTrade::CLASS_BDR)
                ->orWhere('class', StockTrade::CLASS_ETF);
        })->where('is_day_trade', false)
            ->where('is_exempt', false)
            ->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_BR_BDR_ETF_STOCK_IR);
    }

    protected function calculateDayTradeDarf(User $user, array $darfs): void
    {
        $stockTrades = $user->stockTrades()->where('is_day_trade', true)->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_DAY_TRADE_IR);
    }

    protected function calculateDarfsFromStockTrades(Collection $stockTrades, array $darfs, int $percentageIR, ?int $amountSellLimit = null): void
    {
        $period = null;
        $amountIR = 0;
        $amountSold = 0;
        $amountProfit = 0;
        foreach ($stockTrades as $stockTrade) {
            if ($stockTrade->operation !== StockTrade::OPERATION_SELL) {
                continue;
            }

            $currentPeriod = $stockTrade->date->format('Y-m');
            if ($period != $currentPeriod) {
                if ($amountProfit > 0 && $amountSold > ($amountSellLimit ?? -1)) {
                    $darfs[$period] = ($darfs[$period] ?? 0) + ($amountProfit * $percentageIR / 100 - $amountIR);

                    $amountIR = 0;
                }

                if ($amountProfit > 0) {
                    $amountProfit = 0;
                }

                $period = $currentPeriod;
                $amountSold = 0;
            }

            $buyTrades = $stockTrades->where('stock_symbol', $stockTrade->stock_symbol)
                ->where('operation', StockTrade::OPERATION_BUY)
                ->where('date', '<=', $stockTrade->date);

            $averagePrice = $buyTrades->sum(function ($trade) {
                return $trade->price * $trade->quantity;
            }) / $buyTrades->sum('quantity');

            $amountProfit += $stockTrade->price * $stockTrade->quantity - $averagePrice * $stockTrade->quantity;
            $amountSold += $stockTrade->price * $stockTrade->quantity;
            $amountIR += $stockTrade->ir;
        }
    }
}
