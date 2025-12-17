<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'job_id',
        'candidate_id',
        'source',
        'agent_name',
        'status',
        'decision_category',
        'decision_reason',
        'memo',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}

    /**
     * この応募で有効な選考ステップ一覧（company設定準拠）
     */
    public function availableSteps()
    {
        return SelectionStep::where('company_id', $this->company_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * 現在ステータスが有効か判定
     */
    public function isValidStatus(): bool
    {
        return $this->availableSteps()
            ->pluck('key')
            ->contains($this->status);
    }

    /**
     * 次の選考ステップを取得
     */
    public function nextStep(): ?SelectionStep
    {
        $steps = $this->availableSteps()->values();
        $currentIndex = $steps->search(fn ($s) => $s->key === $this->status);

        if ($currentIndex === false) {
            return null;
        }

        return $steps->get($currentIndex + 1);
    }

    /**
     * ステータス遷移（正当性チェック付き）
     */
    public function transitionTo(string $nextKey): bool
    {
        $allowed = $this->availableSteps()->pluck('key')->toArray();

        if (!in_array($nextKey, $allowed, true)) {
            return false;
        }

        $this->status = $nextKey;
        $this->save();

        return true;
    }
