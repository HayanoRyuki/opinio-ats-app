<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Page;

class Job extends Model
{
    protected $fillable = [
        'company_id',
        'job_category_id',
        'title',
        'location',
        'employment_type',
        'salary',
        'working_hours',
        'description',
        'requirements',
        'benefits',
        'notes',
        'status',
        'share_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * この求人で使われる選考ステップ（company_id 経由）
     */
    public function selectionSteps()
    {
        return $this->hasMany(
            SelectionStep::class,
            'company_id',
            'company_id'
        )->orderBy('order');
    }

    /**
     * この求人に紐づく CMS ページ
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    /**
     * ATS共有用トークンを生成
     */
    public function generateShareToken(): void
    {
        $this->update([
            'share_token' => Str::random(40),
        ]);
    }

    /**
     * 公開中の CMS ページが存在するか
     */
    public function hasPublishedPage(): bool
    {
        return $this->pages()
            ->published()
            ->exists();
    }
}
