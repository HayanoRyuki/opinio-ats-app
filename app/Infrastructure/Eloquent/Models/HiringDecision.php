<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HiringDecision extends Model
{
    protected $fillable = [
        'application_id',
        'decided_by',
        'decision',
        'reason',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];
}
