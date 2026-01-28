<?php

namespace App\Models;

use App\Enums\IntakeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Recommendation - エージェント推薦
 *
 * 人材紹介エージェントからの推薦情報。
 */
class Recommendation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id',
        'job_id',
        'application_intake_id',
        'agent_company_name',
        'agent_name',
        'agent_email',
        'candidate_data',
        'recommendation_comment',
        'status',
        'received_at',
    ];

    protected $casts = [
        'status' => IntakeStatus::class,
        'candidate_data' => 'array',
        'received_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty();
    }

    // ========================================
    // Relations
    // ========================================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function applicationIntake(): BelongsTo
    {
        return $this->belongsTo(ApplicationIntake::class);
    }
}
