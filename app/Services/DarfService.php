<?php

namespace App\Services;

use App\Enum\DarfStatus;
use App\Enum\StockTradeClass;
use App\Enum\StockTradeOperation;
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

        $swingTradeDarfs = $this->calculateSwingTradeDarfValues($user);
        $dayTradeDarfs = $this->calculateDayTradeDarfValues($user);

        $darfs = array_merge($swingTradeDarfs, $dayTradeDarfs);

        $collection = collect($darfs)->keyBy('date');

        if ($modelAsArray) {
            return array_map(function ($darf) {
                return $darf->toArray();
            }, $darfs);
        }

        return $darfs;
    }

    protected function calculateDayTradeDarfValues(User $user): array
    {
        $darfs = [];
        $this->calculateDayTradeDarf($user, $darfs);

        // Order darfs by date
        usort($darfs, function ($a, $b) {
            return $a->date <=> $b->date;
        });

        $this->consolidateDarfsBelowMinimumValue($darfs);

        return $darfs;
    }

    protected function calculateSwingTradeDarfValues(User $user): array
    {
        $darfs = [];

        $this->calculateBRStocksDarf($user, $darfs);
        $this->calculateFIIDarf($user, $darfs);
        $this->calculateBDRAndETFStocksDarf($user, $darfs);

        // Order darfs by date
        usort($darfs, function ($a, $b) {
            return $a->date <=> $b->date;
        });

        $this->consolidateDarfsBelowMinimumValue($darfs);

        return $darfs;
    }

    protected function calculateBRStocksDarf(User $user, array &$darfs): void
    {
        $stockTrades = $user->stockTrades()->where([
            'class' => StockTradeClass::STOCK->value,
            'is_day_trade' => false,
            'is_exempt' => false,
        ])->orderBy('date')->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_BR_BDR_ETF_STOCK_IR, self::BRAZILIAN_STOCK, self::EXEMPTION_BR_STOCK_SELL_LIMIT);
    }

    protected function calculateFIIDarf(User $user, array &$darfs): void
    {
        $stockTrades = $user->stockTrades()->where([
            'class' => StockTradeClass::FII->value,
            'is_day_trade' => false,
            'is_exempt' => false,
        ])->orderBy('date')->get();

        $this->calculateDarfsFromStockTrades($stockTrades, $darfs, self::PERCENTAGE_FII_IR, self::FII);
    }

    protected function calculateBDRAndETFStocksDarf(User $user, array &$darfs): void
    {
        $stockTrades = $user->stockTrades()->where(function ($query) {
            $query->where('class', StockTradeClass::BDR->value)
                ->orWhere('class', StockTradeClass::ETF->value);
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

        $sellTradesGroupedByMonth = $stockTrades
            ->where('operation', StockTradeOperation::SELL->value)
            ->groupBy(function ($trade) {
                return $trade->date->format('Y-m');
            });

        foreach ($sellTradesGroupedByMonth as $period => $trades) {
            $amountIR = 0;
            $amountSold = 0;
            $amountProfit = 0;

            foreach ($trades as $stockTrade) {
                $buyTrades = $stockTrades->where('stock_symbol', $stockTrade->stock_symbol)
                    ->where('operation', StockTradeOperation::BUY->value)
                    ->where('date', '<=', $stockTrade->date);

                $averagePrice = $buyTrades->sum(function ($trade) {
                    return $trade->price * $trade->quantity + $trade->fee;
                }) / max($buyTrades->sum('quantity'), 1);

                $stockSold = $stockTrade->price * $stockTrade->quantity - $stockTrade->fee;
                $stockProfit = $stockSold - $averagePrice * $stockTrade->quantity;

                $amountProfit += $stockProfit;
                $amountSold += $stockSold;
                $amountIR += $this->calculateIRRF($stockTrade, $stockProfit);
            }

            if (!Arr::get($darfs, $period)) {
                $darfs[$period] = new Darf([
                    'date' => Carbon::parse($period . '-01'),
                    'user_id' => $stockTrade->user_id,
                    'status' => DarfStatus::PENDING->value,
                    'value' => 0,
                    'brazilian_stock_profit' => 0,
                    'fii_profit' => 0,
                    'bdr_and_etf_profit' => 0,
                    'day_trade_profit' => 0,
                    'ir' => 0,
                ]);
            }

            $darfs[$period]->ir += $amountIR;

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
                $darfs[$period]->value += $amountProfit * $percentageIR / 100 - $darfs[$period]->ir;
            }
        }
    }

    protected function consolidateDarfsBelowMinimumValue(array &$darfs): void
    {
        $accumulatedValue = 0;
        $accumulatedIr = 0;
        foreach ($darfs as $key => $darf) {
            if ($darf->value + $accumulatedValue < self::DARF_MINIMUM_VALUE) {
                $accumulatedValue += $darf->value;
                $accumulatedIr += $darf->ir;
                unset($darfs[$key]);

                continue;
            }

            $darf->value += $accumulatedValue - $accumulatedIr;

            $accumulatedValue = 0;
            $accumulatedIr = 0;
        }
    }

    protected function calculateIRRF(StockTrade $stockTrade, float $stockProfit): float
    {
        if ($stockTrade->is_exempt) {
            return 0;
        }

        if ($stockTrade->operation !== StockTradeOperation::SELL->value) {
            return 0;
        }

        if (!$stockTrade->is_day_trade) {
            $amountWithTaxes = $stockTrade->price * $stockTrade->quantity * -1 + $stockTrade->fee;

            return abs($amountWithTaxes) * 0.00005;
        }

        if ($stockProfit <= 0) {
            return 0;
        }

        return $stockProfit * 0.01;
    }
}
