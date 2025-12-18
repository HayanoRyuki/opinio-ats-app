<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRole extends Model
{
    protected $fillable = [
        'company_id',
        'internal_name',
        'display_name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 並び順スコープ
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
