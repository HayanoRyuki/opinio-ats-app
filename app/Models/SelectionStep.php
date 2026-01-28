<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'key',
        'label',
        'order',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * このステップに属する応募一覧
     * ★ pipeline の正は selection_step_id
     */
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class);
    }
}
