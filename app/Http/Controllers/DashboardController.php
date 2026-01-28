<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // role は VerifyJwt middleware で積まれている
        $role = $request->attributes->get('role');

        /*
         |--------------------------------------------------------------------------
         | 今すぐのアクション
         |--------------------------------------------------------------------------
         | ※ 現時点で「存在が確定しているモデル」のみを使用
         | ※ Interview / Schedule 系は未定義なので 0 固定
         |
         */
        $actions = [
            // 日程調整待ち（未実装）
            'schedule_waiting' => 0,

            // 評価未入力（未実装）
            'evaluation_pending' => 0,

            // 応募後の返信待ち（Application は存在）
            'reply_waiting' => Application::where('status', 'pending')->count(),

            // 長期滞留（未実装）
            'long_stagnation' => 0,
        ];

        return view('dashboard.index', [
            'role'    => $role,
            'actions' => $actions,
        ]);
    }
}
