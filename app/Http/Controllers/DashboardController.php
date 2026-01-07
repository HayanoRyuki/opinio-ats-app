<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterviewSchedule;
use App\Models\Interview;
use App\Models\Message;
use App\Models\Candidate;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // role は VerifyJwt middleware で積まれている
        $role = $request->attributes->get('role');

        // 今すぐのアクション（全ロール共通・まずは admin/recruiter 想定）
        $actions = [
            'schedule_waiting' => InterviewSchedule::where('status', 'waiting')->count(),

            'evaluation_pending' => Interview::whereNotNull('completed_at')
                ->whereDoesntHave('evaluations')
                ->count(),

            'reply_waiting' => Message::whereNotNull('sent_at')
                ->whereNull('replied_at')
                ->where('sent_at', '<', now()->subDays(2))
                ->count(),

            'stagnant_candidates' => Candidate::where(
                'status_updated_at',
                '<',
                now()->subDays(7)
            )->count(),
        ];

        return view('dashboard.index', [
            'role'    => $role,
            'actions' => $actions,
        ]);
    }
}
