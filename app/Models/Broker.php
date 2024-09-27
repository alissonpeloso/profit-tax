<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Broker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identifier',
    ];

    public function stockTrades(): HasMany
    {
        return $this->hasMany(StockTrade::class);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where('name', 'ilike', "%$search%")
            ->orWhere('identifier', 'ilike', "%$search%");
    }
}
