<?php

namespace App\Models;

use App\Enums\IntakeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * MediaApplicationIntake - メディア経由応募
 *
 * 求人媒体経由での応募情報。
 */
class MediaApplicationIntake extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id',
        'media_source_id',
        'job_id',
        'application_intake_id',
        'external_application_id',
        'external_job_id',
        'candidate_data',
        'status',
        'synced_at',
    ];

    protected $casts = [
        'status' => IntakeStatus::class,
        'candidate_data' => 'array',
        'synced_at' => 'datetime',
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

    public function mediaSource(): BelongsTo
    {
        return $this->belongsTo(MediaSource::class);
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
