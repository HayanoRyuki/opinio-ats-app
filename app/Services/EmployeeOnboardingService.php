<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Employee;

class EmployeeOnboardingService
{
    public function onboard(Application $application): ?Employee
    {
        // すでに employee が存在する場合は何もしない
        if (Employee::where('candidate_id', $application->candidate_id)->exists()) {
            return null;
        }

        // status が「入社確定」以外なら作らない
        if ($application->status !== 'offer_accepted') {
            return null;
        }

        return Employee::create([
            'candidate_id' => $application->candidate_id,
            'company_id'   => $application->company_id,
            'joined_at'    => now()->toDateString(),
            'status'       => 'active',
        ]);
    }
}
