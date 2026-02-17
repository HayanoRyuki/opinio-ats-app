<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}
