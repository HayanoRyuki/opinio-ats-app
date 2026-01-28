<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'candidate_id',
        'company_id',
        'joined_at',
        'status',
    ];

    public function candidate()
{
    return $this->belongsTo(\App\Models\Candidate::class);
}
}
