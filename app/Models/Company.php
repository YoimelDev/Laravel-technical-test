<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'document_number',
        'document_type',
        'status',
        'user_id',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
        'status' => CompanyStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activityTypes()
    {
        return $this->belongsToMany(ActivityType::class, 'company_activity_type');
    }

    public function contactInformation()
    {
        return $this->morphOne(ContactInformation::class, 'contactable');
    }
}
