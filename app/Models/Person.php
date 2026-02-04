<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Person - SoT エンティティ
 *
 * 実在する人物の一意表現。
 * 複数の Candidate（企業別候補者）を持つことができる。
 */
class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'persons';

    protected $fillable = [
        'email',
        'phone',
        'name',
        'name_kana',
        'profile',
    ];

    protected $casts = [
        'profile' => 'array',
    ];

    // ========================================
    // Relations
    // ========================================

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }
}
