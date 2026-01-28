<?php

namespace App\Models;

use App\Enums\ApplicationStepStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * ApplicationStep - 応募の選考進捗
 *
 * 応募ごとの各選考ステップの進捗状況を記録。
 */
class ApplicationStep extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'application_id',
        'selection_step_id',
        'status',
        'started_at',
        'completed_at',
        'scheduled_at',
        'notes',
        'evaluated_by',
        'evaluation',
    ];

    protected $casts = [
        'status' => ApplicationStepStatus::class,
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'evaluation' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    // ========================================
    // Relations
    // ========================================

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function selectionStep(): BelongsTo
    {
        return $this->belongsTo(SelectionStep::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    // ========================================
    // Methods
    // ========================================

    public function start(): void
    {
        $this->update([
            'status' => ApplicationStepStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    public function pass(?int $evaluatedBy = null, ?array $evaluation = null): void
    {
        $this->update([
            'status' => ApplicationStepStatus::PASSED,
            'completed_at' => now(),
            'evaluated_by' => $evaluatedBy,
            'evaluation' => $evaluation,
        ]);
    }

    public function fail(?int $evaluatedBy = null, ?array $evaluation = null): void
    {
        $this->update([
            'status' => ApplicationStepStatus::FAILED,
            'completed_at' => now(),
            'evaluated_by' => $evaluatedBy,
            'evaluation' => $evaluation,
        ]);
    }

    public function skip(): void
    {
        $this->update([
            'status' => ApplicationStepStatus::SKIPPED,
            'completed_at' => now(),
        ]);
    }
}
