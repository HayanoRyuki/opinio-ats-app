<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HiringDecision extends Model
{
    protected $fillable = [
        'application_id',
        'decision',
        'reason',
        'decided_by',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
