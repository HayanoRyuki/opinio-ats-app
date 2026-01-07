<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 未ログイン対策（暫定）
        if (! $user) {
            abort(401, '未ログインです。');
        }

        // 面接官はアクセス不可（×）
        if ($user->role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        // 管理者・採用担当は閲覧可
        return view('jobs.index');
    }

    public function show($id)
    {
        $user = auth()->user();

        if (! $user) {
            abort(401, '未ログインです。');
        }

        if ($user->role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        return view('jobs.show', ['id' => $id]);
    }
}
