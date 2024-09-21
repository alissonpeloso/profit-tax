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
            ->where('brokers.name', 'like', "%$search%")
            ->orWhere('date', 'like', "%$search%")
            ->orWhere('stock_symbol', 'like', "%$search%")
            ->orWhere('quantity', 'like', "%$search%")
            ->orWhere('price', 'like', "%$search%")
            ->orWhere('fee', 'like', "%$search%")
            ->orWhere('ir', 'like', "%$search%")
            ->orWhere('note_id', 'like', "%$search%")
            ->orWhere('operation', 'like', "%$search%");
    }
}
