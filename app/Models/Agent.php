<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Agent - エージェント（人材紹介会社の担当者）
 *
 * 人材エージェント（人）および AI エージェントの管理。
 */
class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'agent_type',
        'name',
        'organization',
        'email',
        'phone',
        'notes',
        'is_active',
        'recommendation_token',
        'token_expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    // ========================================
    // Scopes
    // ========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // ========================================
    // Relations
    // ========================================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    // ========================================
    // Helpers
    // ========================================

    public function isHuman(): bool
    {
        return $this->agent_type === 'human';
    }

    public function isAi(): bool
    {
        return $this->agent_type === 'ai';
    }

    /**
     * 表示用ラベル（会社名 / 担当者名）
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->organization} / {$this->name}";
    }

    /**
     * 推薦トークンを生成して保存
     */
    public function generateRecommendationToken(int $expirationDays = 365): string
    {
        $token = Str::random(64);
        $this->update([
            'recommendation_token' => $token,
            'token_expires_at' => now()->addDays($expirationDays),
        ]);

        return $token;
    }

    /**
     * 推薦フォームURLを取得
     */
    public function getRecommendationUrlAttribute(): ?string
    {
        if (!$this->recommendation_token) {
            return null;
        }

        return url("/recommend/{$this->recommendation_token}");
    }

    /**
     * トークンが有効かどうか
     */
    public function hasValidToken(): bool
    {
        if (!$this->recommendation_token) {
            return false;
        }

        if ($this->token_expires_at && $this->token_expires_at->isPast()) {
            return false;
        }

        return $this->is_active;
    }

    /**
     * トークンでエージェントを取得
     */
    public static function findByToken(string $token): ?self
    {
        $agent = static::where('recommendation_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$agent || !$agent->hasValidToken()) {
            return null;
        }

        return $agent;
    }
}
