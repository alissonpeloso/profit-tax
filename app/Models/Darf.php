<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Darf extends Model
{
    use HasFactory;

    public const array STATUSES = [
        'pending',
        'paid',
        'canceled',
    ];

    protected $fillable = [
        'date',
        'user_id',
        'value',
        'status',
    ];
}
