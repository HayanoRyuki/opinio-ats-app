<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RecommendationLink - 推薦と候補者/応募の紐付け
 */
class RecommendationLink extends Model
{
    protected $fillable = [
        'recommendation_id',
        'candidate_id',
        'application_id',
        'linked_at',
    ];

    protected $casts = [
        'linked_at' => 'datetime',
    ];

    // ========================================
    // Relations
    // ========================================

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
