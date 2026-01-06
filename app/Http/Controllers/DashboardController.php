<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /**
         * JWT VerifyJwt ミドルウェアで積まれた値を使用する
         * Laravel Auth は一切使わない
         */
        $role = $request->attributes->get('role');

        // 面接官は閲覧不可（×）
        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        return view('dashboard.index');
    }
}
