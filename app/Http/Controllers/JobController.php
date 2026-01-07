<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * 求人一覧
     */
    public function index(Request $request)
    {
        // VerifyJwt middleware で積まれた role を取得
        $role = $request->attributes->get('role');

        // 認証されていない（JWT 不正・未付与）
        if (! $role) {
            abort(401, '未認証です。');
        }

        // 面接官はアクセス不可
        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        // 管理者 / 採用担当は閲覧可
        return view('jobs.index');
    }

    /**
     * 求人詳細
     */
    public function show(Request $request, $id)
    {
        // VerifyJwt middleware で積まれた role を取得
        $role = $request->attributes->get('role');

        // 認証されていない
        if (! $role) {
            abort(401, '未認証です。');
        }

        // 面接官はアクセス不可
        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        return view('jobs.show', [
            'id' => $id,
        ]);
    }
}
