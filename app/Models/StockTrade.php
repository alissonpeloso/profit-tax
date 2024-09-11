<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTrade extends Model
{
    use HasFactory;

    const AVAILABLE_OPERATIONS = [
        0 => 'buy',
        1 => 'sell',
    ];

    protected $fillable = [
        'date',
        'stock_code',
        'quantity',
        'price',
        'fee',
        'ir',
        'note_id',
        'operation',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'fee' => 'decimal:2',
        'ir' => 'decimal:2',
        'operation' => 'string',
    ];
}
