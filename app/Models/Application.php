<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'job_id',
        'candidate_id',
        'source',
        'agent_name',
        'status',
        'decision_category',
        'decision_reason',
        'memo',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
