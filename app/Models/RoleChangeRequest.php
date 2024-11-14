<?php

namespace App\Models;

use App\Enums\RoleChangeRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleChangeRequest extends Model
{
    /** @use HasFactory<\Database\Factories\RoleChangeRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requested_role',
        'status',
        'reason',
        'admin_notes',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'status' => RoleChangeRequestStatus::class,
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
