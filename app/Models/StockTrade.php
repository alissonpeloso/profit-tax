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
    public const array CLASSES = [
        self::CLASS_STOCK => 'Stock',
        self::CLASS_BDR => 'BDR',
        self::CLASS_ETF => 'ETF',
        self::CLASS_FII => 'FII',
    ];
    public const string CLASS_STOCK = 'stock';
    public const string CLASS_BDR = 'bdr';
    public const string CLASS_ETF = 'etf';
    public const string CLASS_FII = 'fii';

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
        'class',
        'is_day_trade',
        'is_exempt',
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
}
