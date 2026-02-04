<?php

namespace App\Models;

use App\Enums\IntakeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IntakeCandidateDraft - 候補者ドラフト
 *
 * ApplicationIntake から抽出した候補者情報。
 * 人間の確認後に SoT（Person, Candidate）へ昇格。
 *
 * 仮応募（is_preliminary = true）の場合：
 * - スカウト反応など、正式応募前の段階
 * - 面談確定などで正式応募に昇格（promoted_at に日時記録）
 */
class IntakeCandidateDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_intake_id',
        'status',
        'is_preliminary',
        'name',
        'email',
        'phone',
        'extracted_data',
        'matched_person_id',
        'matched_candidate_id',
        'confirmed_by',
        'confirmed_at',
        'promoted_at',
    ];

    protected $casts = [
        'status' => IntakeStatus::class,
        'is_preliminary' => 'boolean',
        'extracted_data' => 'array',
        'confirmed_at' => 'datetime',
        'promoted_at' => 'datetime',
    ];

    // ========================================
    // Relations
    // ========================================

    public function applicationIntake(): BelongsTo
    {
        return $this->belongsTo(ApplicationIntake::class);
    }

    public function matchedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'matched_person_id');
    }

    public function matchedCandidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'matched_candidate_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ========================================
    // Methods
    // ========================================

    public function isPending(): bool
    {
        return $this->status === IntakeStatus::PENDING;
    }

    public function isDraft(): bool
    {
        return $this->status === IntakeStatus::DRAFT;
    }

    public function hasPotentialDuplicate(): bool
    {
        return $this->matched_person_id !== null || $this->matched_candidate_id !== null;
    }

    /**
     * 仮応募かどうか
     */
    public function isPreliminary(): bool
    {
        return $this->is_preliminary ?? false;
    }

    /**
     * 正式応募に昇格済みかどうか
     */
    public function isPromoted(): bool
    {
        return $this->is_preliminary && $this->promoted_at !== null;
    }

    /**
     * 仮応募から正式応募に昇格可能かどうか
     */
    public function canPromote(): bool
    {
        return $this->is_preliminary && $this->promoted_at === null;
    }

    /**
     * 仮応募から正式応募に昇格する
     */
    public function promoteToFormal(): void
    {
        if (!$this->canPromote()) {
            throw new \InvalidArgumentException('この応募は昇格できません。');
        }

        $this->update([
            'is_preliminary' => false,
            'promoted_at' => now(),
        ]);

        // 親の ApplicationIntake も更新
        $this->applicationIntake->update([
            'is_preliminary' => false,
        ]);
    }
}
