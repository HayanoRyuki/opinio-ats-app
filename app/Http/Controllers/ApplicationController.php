<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\SelectionStep;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    /**
     * 応募者追加フォーム
     */
    public function create(Job $job)
    {
        abort_if(
            $job->company_id !== auth()->user()->company_id,
            403
        );

        return view('applications.create', compact('job'));
    }

    /**
     * 応募者保存 + OpinioMeet URL 発行
     */
    public function store(Request $request, Job $job)
    {
        abort_if(
            $job->company_id !== auth()->user()->company_id,
            403
        );

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // 最初の選考ステップ
        $firstStep = SelectionStep::where('company_id', $job->company_id)
            ->orderBy('order')
            ->first();

        // OpinioMeet URL（ダミー）
        $opinioMeetUrl = $this->generateOpinioMeetUrl($job);

        // Application 作成
        $application = Application::create([
            'company_id'        => $job->company_id,
            'job_id'            => $job->id,
            'candidate_id'      => null,
            'selection_step_id' => $firstStep->id,
            'status'            => $firstStep->key,
            'opinio_meet_url'   => $opinioMeetUrl,
        ]);

        // 候補者（簡易）
        $application->candidate()->create([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('jobs.pipeline', $job);
    }

    /**
     * 応募ステータス更新
     * 内定承諾（offer_accepted）になったら employee を作成する
     */
    public function updateStatus(Request $request, Application $application)
    {
        abort_if(
            $application->company_id !== auth()->user()->company_id,
            403
        );

        $request->validate([
            'status' => 'required|string',
        ]);

        // ステータス更新
        $application->update([
            'status' => $request->status,
        ]);

        // 内定承諾時のみ employee を作成
        if ($request->status === 'offer_accepted') {
            // すでに employee が存在する場合は作らない
            if (!Employee::where('candidate_id', $application->candidate_id)->exists()) {
                Employee::create([
                    'candidate_id' => $application->candidate_id,
                    'company_id'   => $application->company_id,
                    'joined_at'    => now()->toDateString(),
                    'status'       => 'active',
                ]);
            }
        }

        return back();
    }

    /**
     * OpinioMeet URL 生成（仮）
     */
    protected function generateOpinioMeetUrl(Job $job): string
    {
        $token = Str::random(32);

        // 本番では Opinio API を呼ぶ
        return "https://opinio.example.com/meet/{$job->id}/{$token}";
    }
}
