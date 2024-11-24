<?php

namespace App\Services;

use App\Models\Darf;
use App\Models\StockTrade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Pest\Support\Arr;

class DarfService
{
    const EXEMPTION_BR_STOCK_SELL_LIMIT = 20000;
    const PERCENTAGE_BR_BDR_ETF_STOCK_IR = 15;
    const PERCENTAGE_FII_IR = 20;
    const PERCENTAGE_DAY_TRADE_IR = 20;
    const BRAZILIAN_STOCK = 'brazilian_stock';
    const FII = 'fii';
    const BDR_AND_ETF = 'bdr_and_etf';
    const DAY_TRADE = 'day_trade';
    const DARF_MINIMUM_VALUE = 10;

    public function calculateDarfValues(bool $modelAsArray = false): array
    {
        /** @var User $user */
        $user = auth()->user();

        $darfs = [];
        $this->calculateBRStocksDarf($user, $darfs);
        $this->calculateFIIDarf($user, $darfs);
        $this->calculateBDRAndETFStocksDarf($user, $darfs);
        $this->calculateDayTradeDarf($user, $darfs);

        $this->consolidateDarfsBelowMinimumValue($darfs);

        if ($modelAsArray) {
            return array_map(function ($darf) {
                return $darf->toArray();
            }, $darfs);
        }

        return $darfs;
    }

    protected function calculateBRStocksDarf(User $user, array &$darfs): void
    {
        $stockTrades = $user->stockTrades()->where([
            'class' => StockTrade::CLASS_STOCK,
            'is_day_trade' => false,
            'is_exempt' => false,
        ])->orderBy('date')->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_BR_BDR_ETF_STOCK_IR, self::BRAZILIAN_STOCK, self::EXEMPTION_BR_STOCK_SELL_LIMIT);
    }

    protected function calculateFIIDarf(User $user, array &$darfs): void
    {
        $stockTrades = $user->stockTrades()->where([
            'class' => StockTrade::CLASS_FII,
            'is_day_trade' => false,
            'is_exempt' => false,
        ])->orderBy('date')->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_FII_IR, self::FII);
    }

    protected function calculateBDRAndETFStocksDarf(User $user, array &$darfs): void
    {
        $stockTrades = $user->stockTrades()->where(function ($query) {
            $query->where('class', StockTrade::CLASS_BDR)
                ->orWhere('class', StockTrade::CLASS_ETF);
        })->where('is_day_trade', false)
            ->where('is_exempt', false)
            ->orderBy('date')->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_BR_BDR_ETF_STOCK_IR, self::BDR_AND_ETF);
    }

    protected function calculateDayTradeDarf(User $user, array &$darfs): void
    {
        $stockTrades = $user->stockTrades()->where('is_day_trade', true)->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_DAY_TRADE_IR, self::DAY_TRADE);
    }

    protected function calculateDarfsFromStockTrades(Collection $stockTrades, array &$darfs, int $percentageIR, string $stockTradeType, ?int $amountSellLimit = null): void
    {
        if ($stockTrades->isEmpty()) {
            return;
        }

        $sellTradesGroupedByMonth = $stockTrades->where('operation', StockTrade::OPERATION_SELL)->groupBy(function ($trade) {
            return $trade->date->format('Y-m');
        });

        foreach ($sellTradesGroupedByMonth as $period => $trades) {
            $amountIR = 0;
            $amountSold = 0;
            $amountProfit = 0;

            foreach ($trades as $stockTrade) {
                $buyTrades = $stockTrades->where('stock_symbol', $stockTrade->stock_symbol)
                    ->where('operation', StockTrade::OPERATION_BUY)
                    ->where('date', '<=', $stockTrade->date);

                $averagePrice = $buyTrades->sum(function ($trade) {
                    return $trade->price * $trade->quantity;
                }) / max($buyTrades->sum('quantity'), 1);

                $amountProfit += $stockTrade->price * $stockTrade->quantity - $averagePrice * $stockTrade->quantity;
                $amountSold += $stockTrade->price * $stockTrade->quantity;
                $amountIR += $stockTrade->ir;
            }

            if (!Arr::get($darfs, $period)) {
                $darfs[$period] = new Darf([
                    'date' => Carbon::parse($period . '-01'),
                    'user_id' => $stockTrade->user_id,
                    'status' => Darf::STATUS_PENDING,
                    'value' => 0,
                    'brazilian_stock_profit' => 0,
                    'fii_profit' => 0,
                    'bdr_and_etf_profit' => 0,
                    'day_trade_profit' => 0,
                ]);
            }

            switch ($stockTradeType) {
                case self::BRAZILIAN_STOCK:
                    $darfs[$period]->brazilian_stock_profit += $amountProfit;
                    break;
                case self::FII:
                    $darfs[$period]->fii_profit += $amountProfit;
                    break;
                case self::BDR_AND_ETF:
                    $darfs[$period]->bdr_and_etf_profit += $amountProfit;
                    break;
                case self::DAY_TRADE:
                    $darfs[$period]->day_trade_profit += $amountProfit;
                    break;
            }

            if ($amountProfit > 0 && $amountSold > ($amountSellLimit ?? -1)) {
                $darfs[$period]->value += $amountProfit * $percentageIR / 100 - $amountIR;
            }
        }
    }

    protected function consolidateDarfsBelowMinimumValue(array &$darfs): void
    {
        $accumulatedValue = 0;
        foreach ($darfs as $key => $darf) {
            if ($darf->value + $accumulatedValue < self::DARF_MINIMUM_VALUE) {
                $accumulatedValue += $darf->value;
                unset($darfs[$key]);

                continue;
            }

            $darf->value += $accumulatedValue;
            $accumulatedValue = 0;
        }
    }
}
