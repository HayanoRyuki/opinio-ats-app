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
        'selection_step_id',
        'status',
        'opinio_meet_url',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function selectionStep()
    {
        return $this->belongsTo(SelectionStep::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * 評価一覧
     */
    public function evaluations()
    {
        return $this->hasMany(\App\Models\Evaluation::class);
    }

    /**
     * 採用判断（Decide Domain）
     */
    public function hiringDecision()
    {
        return $this->hasOne(\App\Models\HiringDecision::class);
    }

    /**
     * ★ 評価済みか？
     */
    public function isEvaluated(): bool
    {
        return $this->evaluations()->exists();
    }
}
