<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broker extends Model
{
    use HasFactory;

    const AVAILABLE_BROKER_IDENTIFIERS = [
        'rico',
        'nuinvest',
    ];

    protected $fillable = [
        'name',
        'identifier',
    ];
}
