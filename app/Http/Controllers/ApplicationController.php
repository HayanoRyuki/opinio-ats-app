<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\SelectionStep;
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
     * OpinioMeet URL 生成（仮）
     */
    protected function generateOpinioMeetUrl(Job $job): string
    {
        $token = Str::random(32);

        // 本番では Opinio API を呼ぶ
        return "https://opinio.example.com/meet/{$job->id}/{$token}";
    }
}
