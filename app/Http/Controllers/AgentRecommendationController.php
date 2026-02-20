<?php

namespace App\Http\Controllers;

use App\Enums\IntakeChannel;
use App\Enums\IntakeStatus;
use App\Models\Agent;
use App\Models\ApplicationIntake;
use App\Models\IntakeCandidateDraft;
use App\Models\Job;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

/**
 * エージェント推薦フォーム（公開・認証不要）
 *
 * エージェント登録時に発行されたトークン付きURLからアクセスする。
 */
class AgentRecommendationController extends Controller
{
    /**
     * 推薦フォーム表示
     *
     * GET /recommend/{token}
     */
    public function form(string $token): Response
    {
        $agent = Agent::findByToken($token);

        if (!$agent) {
            abort(404, 'このリンクは無効です。');
        }

        // この会社の公開中の求人を取得
        $jobs = Job::where('company_id', $agent->company_id)
            ->where('status', 'open')
            ->select('id', 'title', 'location', 'employment_type')
            ->orderBy('title')
            ->get();

        return Inertia::render('AgentRecommend/Form', [
            'agent' => [
                'organization' => $agent->organization,
                'name' => $agent->name,
            ],
            'company' => [
                'name' => $agent->company->name,
            ],
            'jobs' => $jobs,
            'token' => $token,
        ]);
    }

    /**
     * 推薦送信処理
     *
     * POST /recommend/{token}
     */
    public function store(string $token, Request $request)
    {
        $agent = Agent::findByToken($token);

        if (!$agent) {
            abort(404, 'このリンクは無効です。');
        }

        $validator = Validator::make($request->all(), [
            'candidate_name' => ['required', 'string', 'max:255'],
            'candidate_email' => ['nullable', 'email', 'max:255'],
            'candidate_phone' => ['nullable', 'string', 'max:50'],
            'job_id' => ['nullable', 'integer', 'exists:recruitment_jobs,id'],
            'recommendation_comment' => ['nullable', 'string', 'max:5000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // job_id が指定されている場合、この会社の求人か確認
        $job = null;
        if (!empty($data['job_id'])) {
            $job = Job::where('id', $data['job_id'])
                ->where('company_id', $agent->company_id)
                ->where('status', 'open')
                ->first();

            if (!$job) {
                return back()->withErrors(['job_id' => '指定された求人が見つかりません。'])->withInput();
            }
        }

        // ファイルアップロード処理
        $resumeUrl = null;
        if ($request->hasFile('resume')) {
            $resumeUrl = $request->file('resume')->store(
                "resumes/{$agent->company_id}",
                'public'
            );
        }

        try {
            $result = DB::transaction(function () use ($agent, $data, $job, $resumeUrl) {
                // ApplicationIntake 作成
                $intake = ApplicationIntake::create([
                    'company_id' => $agent->company_id,
                    'job_id' => $job?->id,
                    'channel' => IntakeChannel::AGENT,
                    'source_id' => 'agent_form_' . now()->timestamp . '_' . uniqid(),
                    'raw_data' => [
                        'agent_id' => $agent->id,
                        'agent_company' => $agent->organization,
                        'agent_name' => $agent->name,
                        'agent_email' => $agent->email,
                        'candidate' => [
                            'name' => $data['candidate_name'],
                            'email' => $data['candidate_email'] ?? null,
                            'phone' => $data['candidate_phone'] ?? null,
                            'resume_url' => $resumeUrl,
                        ],
                        'recommendation_comment' => $data['recommendation_comment'] ?? null,
                    ],
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                // IntakeCandidateDraft 作成
                $draft = IntakeCandidateDraft::create([
                    'application_intake_id' => $intake->id,
                    'name' => $data['candidate_name'],
                    'email' => $data['candidate_email'] ?? null,
                    'phone' => $data['candidate_phone'] ?? null,
                    'extracted_data' => [
                        'resume_url' => $resumeUrl,
                        'recommendation' => $data['recommendation_comment'] ?? null,
                        'agent_company' => $agent->organization,
                        'agent_name' => $agent->name,
                        'agent_email' => $agent->email,
                    ],
                ]);

                // Recommendation レコード作成
                $recommendation = Recommendation::create([
                    'company_id' => $agent->company_id,
                    'agent_id' => $agent->id,
                    'job_id' => $job?->id,
                    'application_intake_id' => $intake->id,
                    'agent_company_name' => $agent->organization,
                    'agent_name' => $agent->name,
                    'agent_email' => $agent->email,
                    'candidate_data' => [
                        'name' => $data['candidate_name'],
                        'email' => $data['candidate_email'] ?? null,
                        'phone' => $data['candidate_phone'] ?? null,
                        'resume_url' => $resumeUrl,
                    ],
                    'recommendation_comment' => $data['recommendation_comment'] ?? null,
                    'status' => IntakeStatus::RECEIVED->value,
                    'received_at' => now(),
                ]);

                // Intake のステータスを処理中に更新
                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'recommendation_id' => $recommendation->id,
                ];
            });

            Log::info('エージェント推薦フォームから推薦を受け付けました', [
                'agent_id' => $agent->id,
                'result' => $result,
            ]);

            return redirect("/recommend/{$token}/thanks");

        } catch (\Exception $e) {
            Log::error('エージェント推薦フォームの処理に失敗しました', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['general' => '送信中にエラーが発生しました。しばらくしてから再度お試しください。'])->withInput();
        }
    }

    /**
     * 送信完了ページ
     *
     * GET /recommend/{token}/thanks
     */
    public function thanks(string $token): Response
    {
        $agent = Agent::findByToken($token);

        if (!$agent) {
            abort(404, 'このリンクは無効です。');
        }

        return Inertia::render('AgentRecommend/Thanks', [
            'agent' => [
                'organization' => $agent->organization,
                'name' => $agent->name,
            ],
            'company' => [
                'name' => $agent->company->name,
            ],
            'token' => $token,
        ]);
    }
}
