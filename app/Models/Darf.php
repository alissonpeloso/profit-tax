<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'brazilian_stock_profit' => 'float',
        'fii_profit' => 'float',
        'bdr_and_etf_profit' => 'float',
        'day_trade_profit' => 'float',
        'value' => 'float',
    ];
    protected $appends = [
        'due_date',
    ];

    protected function getDueDateAttribute()
    {
        return $this->due_date = $this->date->addMonth()->endOfMonth();
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'due_date' => $this->due_date,
            'brazilian_stock_profit' => $this->brazilian_stock_profit,
            'fii_profit' => $this->fii_profit,
            'bdr_and_etf_profit' => $this->bdr_and_etf_profit,
            'day_trade_profit' => $this->day_trade_profit,
            'value' => $this->value,
            'status' => $this->status,
        ];
    }

    public function scopeSearch($query, $search)
    {
        return $query
            ->where('date', 'ilike', "%$search%")
            ->orWhere('brazilian_stock_profit', 'ilike', "%$search%")
            ->orWhere('fii_profit', 'ilike', "%$search%")
            ->orWhere('bdr_and_etf_profit', 'ilike', "%$search%")
            ->orWhere('day_trade_profit', 'ilike', "%$search%")
            ->orWhere('value', 'ilike', "%$search%")
            ->orWhere('status', 'ilike', "%$search%");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
