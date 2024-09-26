<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTrade extends Model
{
    use HasFactory;

    public const OPERATIONS = [
        'buy',
        'sell',
    ];

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
}
