<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Job;

class Page extends Model
{
    protected $fillable = [
        'job_id',
        'title',
        'slug',
        'content',
        'status',
    ];

    /**
     * 紐づく求人
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * 公開中かどうか
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * 公開ページのみのスコープ
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
