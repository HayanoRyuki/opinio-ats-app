<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Inertia\Inertia;

class JobController extends Controller
{
    /**
     * 求人一覧
     */
    public function index(Request $request)
    {
        $role = $request->attributes->get('role');

        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        $jobs = Job::orderBy('created_at', 'desc')->get();

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * 求人作成フォーム
     */
    public function create(Request $request)
    {
        $role = $request->attributes->get('role');

        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        return Inertia::render('Jobs/Create');
    }

    /**
     * 求人詳細
     */
    public function show(Request $request, $id)
    {
        $role = $request->attributes->get('role');

        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        $job = Job::findOrFail($id);

        return Inertia::render('Jobs/Show', [
            'job' => $job,
        ]);
    }

    /**
     * 求人編集フォーム
     */
    public function edit(Request $request, $id)
    {
        $role = $request->attributes->get('role');

        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        $job = Job::findOrFail($id);

        return Inertia::render('Jobs/Edit', [
            'job' => $job,
        ]);
    }
}
