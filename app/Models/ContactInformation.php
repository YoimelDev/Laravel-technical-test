<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{
    /** @use HasFactory<\Database\Factories\ContactInformationFactory> */
    use HasFactory;

    protected $fillable = [
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
    ];

    public function contactable()
    {
        return $this->morphTo();
    }
}
