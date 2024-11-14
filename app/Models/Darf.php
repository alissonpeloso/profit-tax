<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Darf extends Model
{
    use HasFactory;

    protected $table = 'darfs';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PAID,
        self::STATUS_CANCELED,
    ];
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'date',
        'due_date',
        'brazilian_stock_profit',
        'fii_profit',
        'bdr_and_etf_profit',
        'day_trade_profit',
        'user_id',
        'value',
        'status',
    ];
    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'brazilian_stock_profit' => 'float',
    ];

    protected function calculateDueDate(): void
    {
        $this->due_date = $this->date->addMonth()->endOfMonth();
    }
}
