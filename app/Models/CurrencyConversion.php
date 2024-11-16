<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyConversion extends Model
{
    /** @use HasFactory<\Database\Factories\CurrencyConversionFactory> */
    use HasFactory;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'amount',
        'converted_amount',
        'rate',
        'user_id',
        'conversion_date',
    ];

    protected $casts = [
        'amount' => 'float',
        'converted_amount' => 'float',
        'rate' => 'float',
        'conversion_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
