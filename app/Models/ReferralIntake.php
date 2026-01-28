<?php

namespace App\Models;

use App\Enums\IntakeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * ReferralIntake - リファラル（社員紹介）
 *
 * 社員からの紹介情報。
 */
class ReferralIntake extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id',
        'job_id',
        'application_intake_id',
        'referrer_id',
        'referred_name',
        'referred_email',
        'referred_phone',
        'relationship',
        'recommendation_reason',
        'status',
    ];

    protected $casts = [
        'status' => IntakeStatus::class,
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

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function applicationIntake(): BelongsTo
    {
        return $this->belongsTo(ApplicationIntake::class);
    }
}
