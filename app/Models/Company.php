<?php

namespace App\Models;

use App\Models\JobRole;

class Company extends BaseModel
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'domain',
        'address',
        'phone',
        'website',
        'industry',
    ];

    /**
     * この会社に紐づく職種（ATSマスター）
     */
    public function jobRoles()
    {
        return $this->hasMany(JobRole::class);
    }
}
