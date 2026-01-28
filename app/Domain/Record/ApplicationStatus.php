<?php

namespace App\Domain\Record;

final class ApplicationStatus
{
    public const APPLIED               = 'applied';
    public const SCREENING             = 'screening';
    public const INTERVIEW             = 'interview';
    public const INTERVIEW_COMPLETED   = 'interview_completed';
    public const OFFER_SENT            = 'offer_sent';
    public const HIRED                 = 'hired';
    public const WITHDRAWN             = 'withdrawn';

    public static function all(): array
    {
        return [
            self::APPLIED,
            self::SCREENING,
            self::INTERVIEW,
            self::INTERVIEW_COMPLETED,
            self::OFFER_SENT,
            self::HIRED,
            self::WITHDRAWN,
        ];
    }
}
