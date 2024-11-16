<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalRate extends Model
{
    /** @use HasFactory<\Database\Factories\HistoricalRateFactory> */
    use HasFactory;

    protected $fillable = [
        'rate_date',
        'currency',
        'rate',
    ];

    protected $casts = [
        'rate_date' => 'date',
        'rate' => 'decimal:8',
    ];
}
