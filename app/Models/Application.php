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

    /**
     * 候補者
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * 選考ステップ
     */
    public function selectionStep()
    {
        return $this->belongsTo(SelectionStep::class);
    }

    /**
     * 求人
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * 評価（← ★ここが正しい場所）
     */
    public function evaluations()
    {
        return $this->hasMany(\App\Models\Evaluation::class);
    }
}
