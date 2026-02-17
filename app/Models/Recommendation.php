<?php

namespace App\Models;

use App\Enums\IntakeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Recommendation - エージェント推薦
 *
 * 人材紹介エージェントからの推薦情報。
 * 推薦は「判断の材料であって、判断そのものではない」。
 */
class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'agent_id',
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

    // ========================================
    // Relations
    // ========================================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function applicationIntake(): BelongsTo
    {
        return $this->belongsTo(ApplicationIntake::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(RecommendationLink::class);
    }

    // ========================================
    // Helpers
    // ========================================

    /**
     * エージェント表示名（agent リレーションがあればそちら優先）
     */
    public function getAgentDisplayNameAttribute(): string
    {
        if ($this->agent) {
            return $this->agent->display_name;
        }

        // 後方互換: 旧カラムから表示
        $parts = array_filter([
            $this->agent_company_name,
            $this->agent_name,
        ]);

        return implode(' / ', $parts) ?: '不明';
    }

    /**
     * 紐付け済みの候補者があるか
     */
    public function hasLinkedCandidates(): bool
    {
        return $this->links()->exists();
    }
}
