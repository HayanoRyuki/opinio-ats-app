<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * レポート一覧
     */
    public function index(Request $request)
    {
        // VerifyJwt middleware で積まれた role を取得
        $role = $request->attributes->get('role');

        // 未認証（JWT 未付与 / 不正）
        if (! $role) {
            abort(401, '未認証です。');
        }

        // 面接官はアクセス不可
        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        // 管理者・採用担当はアクセス可
        return view('reports.index');
    }
}
