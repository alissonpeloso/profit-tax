<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTrade extends Model
{
    use HasFactory;

    public const array OPERATIONS = [
        self::OPERATION_BUY => 'Buy',
        self::OPERATION_SELL => 'Sell',
    ];
    public const string OPERATION_BUY = 'buy';
    public const string OPERATION_SELL = 'sell';

    protected $fillable = [
        'user_id',
        'broker_id',
        'date',
        'stock_symbol',
        'quantity',
        'price',
        'fee',
        'ir',
        'note_id',
        'operation',
    ];
    protected $casts = [
        'date' => 'date',
    ];

    public function scopeSearch($query, $search)
    {
        $query->join('brokers', 'stock_trades.broker_id', '=', 'brokers.id')
            ->select('stock_trades.*', 'brokers.name as brokers.name');

        return $query
            ->where('brokers.name', 'ilike', "%$search%")
            ->orWhere('date', 'ilike', "%$search%")
            ->orWhere('stock_symbol', 'ilike', "%$search%")
            ->orWhere('quantity', 'ilike', "%$search%")
            ->orWhere('price', 'ilike', "%$search%")
            ->orWhere('fee', 'ilike', "%$search%")
            ->orWhere('ir', 'ilike', "%$search%")
            ->orWhere('note_id', 'ilike', "%$search%")
            ->orWhere('operation', 'ilike', "%$search%");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * Check if the trade is a day trade. A day trade is a trade that is bought and sold on the same day.
     */
    public function isDayTrade(): bool
    {
        return $this->operation === self::OPERATION_SELL && $this->stockTrades()
            ->where('stock_symbol', $this->stock_symbol)
            ->where('operation', self::OPERATION_BUY)
            ->whereDate('date', $this->date)
            ->exists();
    }
}
