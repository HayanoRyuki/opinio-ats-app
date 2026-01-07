<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 未ログイン対策
        if (! $user) {
            abort(401, '未ログインです。');
        }

        // 面接官はアクセス不可
        if ($user->role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        // 求人一覧（まずは全件取得）
        $jobs = Job::orderBy('created_at', 'desc')->get();

        return view('jobs.index', [
            'jobs' => $jobs,
        ]);
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

        $job = Job::findOrFail($id);

        return view('jobs.show', [
            'job' => $job,
        ]);
    }
}
