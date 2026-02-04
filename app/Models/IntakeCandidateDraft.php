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
 */
class IntakeCandidateDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_intake_id',
        'status',
        'name',
        'email',
        'phone',
        'extracted_data',
        'matched_person_id',
        'matched_candidate_id',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'status' => IntakeStatus::class,
        'extracted_data' => 'array',
        'confirmed_at' => 'datetime',
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
}
