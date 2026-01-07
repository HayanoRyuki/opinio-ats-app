<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interview;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // role は VerifyJwt middleware で積まれている
        $role = $request->attributes->get('role');

        // 今すぐのアクション（既存モデルのみ使用）
        $actions = [
            // InterviewSchedule は未定義なので一旦 0
            'schedule_waiting'   => 0,

            // 面接評価未入力（例：completed_at が null）
            'evaluation_pending' => Interview::whereNull('completed_at')->count(),

            // 応募後の返信待ち（例：ステータスが pending）
            'reply_waiting'      => Application::where('status', 'pending')->count(),

            // 長期滞留（暫定：今は 0）
            'long_stagnation'    => 0,
        ];

        return view('dashboard.index', [
            'role'    => $role,
            'actions' => $actions,
        ]);
    }
}
