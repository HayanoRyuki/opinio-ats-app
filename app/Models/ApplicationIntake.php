<?php

namespace App\Models;

use App\Enums\IntakeChannel;
use App\Enums\IntakeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ApplicationIntake - 応募取り込み生データ
 *
 * 外部チャネルからの応募を一旦格納する。
 * 確認後に SoT（Candidate, Application）へ昇格。
 *
 * チャネル：
 * - direct: 直接応募（採用サイト・LP）→ 正式応募
 * - scout: スカウト（ビズリーチ・Wantedly等）→ 仮応募
 * - agent: エージェント推薦 → 正式応募
 * - referral: リファラル（社員紹介）→ 正式応募
 */
class ApplicationIntake extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'job_id',
        'channel',
        'status',
        'is_preliminary',
        'raw_data',
        'parsed_data',
        'source_id',
        'received_at',
    ];

    protected $casts = [
        'channel' => IntakeChannel::class,
        'status' => IntakeStatus::class,
        'is_preliminary' => 'boolean',
        'raw_data' => 'array',
        'parsed_data' => 'array',
        'received_at' => 'datetime',
    ];

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

    /**
     * 仮応募かどうか（スカウト反応など）
     */
    public function isPreliminary(): bool
    {
        return $this->is_preliminary ?? $this->channel->isPreliminary();
    }

    /**
     * 正式応募かどうか
     */
    public function isFormalApplication(): bool
    {
        return !$this->isPreliminary();
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
