<?php

namespace App\Models;

use App\Enums\IntakeChannel;
use App\Enums\IntakeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * ApplicationIntake - 応募取り込み生データ
 *
 * 外部チャネルからの応募を一旦格納する。
 * 確認後に SoT（Candidate, Application）へ昇格。
 */
class ApplicationIntake extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_id',
        'job_id',
        'channel',
        'status',
        'raw_data',
        'parsed_data',
        'source_id',
        'received_at',
    ];

    protected $casts = [
        'channel' => IntakeChannel::class,
        'status' => IntakeStatus::class,
        'raw_data' => 'array',
        'parsed_data' => 'array',
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

    public function draft(): HasOne
    {
        return $this->hasOne(IntakeCandidateDraft::class);
    }

    // ========================================
    // Methods
    // ========================================

    public function isPending(): bool
    {
        return $this->status === IntakeStatus::PENDING;
    }

    public function markAsDraft(): void
    {
        $this->update(['status' => IntakeStatus::DRAFT]);
    }

    public function markAsConfirmed(): void
    {
        $this->update(['status' => IntakeStatus::CONFIRMED]);
    }

    public function markAsRejected(): void
    {
        $this->update(['status' => IntakeStatus::REJECTED]);
    }
}
