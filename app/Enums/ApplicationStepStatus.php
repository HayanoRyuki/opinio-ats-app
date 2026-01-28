<?php

namespace App\Enums;

enum ApplicationStepStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case PASSED = 'passed';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => '未着手',
            self::IN_PROGRESS => '進行中',
            self::PASSED => '通過',
            self::FAILED => '不通過',
            self::SKIPPED => 'スキップ',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'blue',
            self::PASSED => 'green',
            self::FAILED => 'red',
            self::SKIPPED => 'yellow',
        };
    }
}
