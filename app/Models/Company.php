<?php

namespace App\Models;

use App\Models\Job;
use App\Models\JobRole;

class Company extends BaseModel
{
    protected $table = 'companies';

    protected $fillable = [
    'name',
    'slug',      // ← これを追加
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

    /**
     * この会社に紐づく求人
     */
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
