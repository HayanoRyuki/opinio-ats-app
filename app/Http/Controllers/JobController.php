<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    /**
     * 求人一覧
     * - 管理者 / 採用担当：閲覧可
     * - 面接官：閲覧不可
     */
    public function index()
    {
        $user = auth()->user();

        // 面接官はアクセス不可
        if ($user->role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        // 求人一覧（暫定：全件）
        $jobs = Job::orderBy('created_at', 'desc')->get();

        return view('jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * 求人詳細
     * - 管理者 / 採用担当：閲覧可
     * - 面接官：閲覧不可
     */
    public function show($id)
    {
        $user = auth()->user();

        // 面接官はアクセス不可
        if ($user->role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        $job = Job::findOrFail($id);

        return view('jobs.show', [
            'job' => $job,
        ]);
    }
}
