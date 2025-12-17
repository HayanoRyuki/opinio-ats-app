<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'company_id',
        'job_category_id',
        'title',
        'description',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * この求人で使われる選考ステップ
     * （company_id 経由）
     */
    public function selectionSteps()
    {
        return $this->hasMany(
            SelectionStep::class,
            'company_id',   // selection_steps.company_id
            'company_id'    // jobs.company_id
        )->orderBy('order');
    }
}
