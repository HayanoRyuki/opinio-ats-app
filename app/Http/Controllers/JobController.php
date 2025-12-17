<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SelectionStep;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * 求人一覧（自社のみ）
     */
    public function index()
    {
        $jobs = Job::where('company_id', auth()->user()->company_id)->get();

        return view('jobs.index', compact('jobs'));
    }

    /**
     * 求人作成フォーム
     */
    public function create()
    {
        return view('jobs.create');
    }

    /**
     * 求人保存 + 選考ステップ初期化
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $companyId = auth()->user()->company_id;

        // 求人作成
        $job = Job::create([
            'company_id'      => $companyId,
            'job_category_id' => null,
            'title'           => $request->title,
            'description'     => $request->description,
            'status'          => 'open',
        ]);

        // 会社に SelectionStep がなければ初期生成
        if (SelectionStep::where('company_id', $companyId)->count() === 0) {

            $defaultSteps = [
                ['key' => 'document', 'label' => '書類選考', 'order' => 1],
                ['key' => 'first',    'label' => '一次面接', 'order' => 2],
                ['key' => 'second',   'label' => '二次面接', 'order' => 3],
                ['key' => 'final',    'label' => '最終面接', 'order' => 4],
                ['key' => 'offer',    'label' => '内定',     'order' => 5],
            ];

            foreach ($defaultSteps as $step) {
                SelectionStep::create([
                    'company_id' => $companyId,
                    'key'        => $step['key'],
                    'label'      => $step['label'],
                    'order'      => $step['order'],
                    'is_active'  => true,
                ]);
            }
        }

        return redirect()->route('jobs.pipeline', $job);
    }
}
