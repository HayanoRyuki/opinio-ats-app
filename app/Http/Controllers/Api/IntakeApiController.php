<?php

namespace App\Http\Controllers\Api;

use App\Enums\IntakeChannel;
use App\Enums\IntakeStatus;
use App\Http\Controllers\Controller;
use App\Models\ApplicationIntake;
use App\Models\IntakeCandidateDraft;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IntakeApiController extends Controller
{
    /**
     * Web応募API - 自社採用サイトからの応募受付
     *
     * POST /api/intake/web
     */
    public function web(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['required', 'integer', 'exists:recruitment_jobs,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'resume_url' => ['nullable', 'url'],
            'cover_letter' => ['nullable', 'string'],
            'profile' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // 求人が指定された企業のものか確認
        $job = Job::where('id', $data['job_id'])
            ->where('company_id', $data['company_id'])
            ->where('status', 'open')
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => '指定された求人が見つからないか、募集が終了しています。',
            ], 404);
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                // ApplicationIntake 作成
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'channel' => IntakeChannel::DIRECT,
                    'source_id' => 'web_' . now()->timestamp . '_' . uniqid(),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                // IntakeCandidateDraft 作成
                $draft = IntakeCandidateDraft::create([
                    'intake_id' => $intake->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'job_id' => $job->id,
                    'profile_data' => array_merge(
                        $data['profile'] ?? [],
                        [
                            'resume_url' => $data['resume_url'] ?? null,
                            'cover_letter' => $data['cover_letter'] ?? null,
                        ]
                    ),
                ]);

                // ステータスを処理中に更新
                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                ];
            });

            Log::info('Web応募を受け付けました', $result);

            return response()->json([
                'success' => true,
                'message' => '応募を受け付けました。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Web応募の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '応募の処理中にエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * エージェントAPI - 人材紹介会社からの推薦受付
     *
     * POST /api/intake/agent
     */
    public function agent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['required', 'integer', 'exists:recruitment_jobs,id'],
            'agent_company' => ['required', 'string', 'max:255'],
            'agent_name' => ['nullable', 'string', 'max:255'],
            'agent_email' => ['nullable', 'email', 'max:255'],
            'candidate' => ['required', 'array'],
            'candidate.name' => ['required', 'string', 'max:255'],
            'candidate.email' => ['nullable', 'email', 'max:255'],
            'candidate.phone' => ['nullable', 'string', 'max:50'],
            'candidate.resume_url' => ['nullable', 'url'],
            'candidate.recommendation' => ['nullable', 'string'],
            'candidate.profile' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // 求人確認
        $job = Job::where('id', $data['job_id'])
            ->where('company_id', $data['company_id'])
            ->where('status', 'open')
            ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'error' => '指定された求人が見つからないか、募集が終了しています。',
            ], 404);
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'channel' => IntakeChannel::AGENT,
                    'source_id' => 'agent_' . now()->timestamp . '_' . uniqid(),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                $candidate = $data['candidate'];
                $draft = IntakeCandidateDraft::create([
                    'intake_id' => $intake->id,
                    'name' => $candidate['name'],
                    'email' => $candidate['email'] ?? null,
                    'phone' => $candidate['phone'] ?? null,
                    'job_id' => $job->id,
                    'profile_data' => array_merge(
                        $candidate['profile'] ?? [],
                        [
                            'resume_url' => $candidate['resume_url'] ?? null,
                            'recommendation' => $candidate['recommendation'] ?? null,
                            'agent_company' => $data['agent_company'],
                            'agent_name' => $data['agent_name'] ?? null,
                            'agent_email' => $data['agent_email'] ?? null,
                        ]
                    ),
                ]);

                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                ];
            });

            Log::info('エージェント推薦を受け付けました', $result);

            return response()->json([
                'success' => true,
                'message' => '推薦を受け付けました。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('エージェント推薦の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '推薦の処理中にエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * スカウトAPI - スカウトサービスからの応募受付
     *
     * POST /api/intake/scout
     */
    public function scout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['nullable', 'integer', 'exists:recruitment_jobs,id'],
            'scout_service' => ['required', 'string', 'max:255'], // ビズリーチ、Wantedly等
            'scout_id' => ['nullable', 'string', 'max:255'],
            'candidate' => ['required', 'array'],
            'candidate.name' => ['required', 'string', 'max:255'],
            'candidate.email' => ['nullable', 'email', 'max:255'],
            'candidate.phone' => ['nullable', 'string', 'max:50'],
            'candidate.profile_url' => ['nullable', 'url'],
            'candidate.profile' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // job_idが指定されている場合は確認
        $job = null;
        if (!empty($data['job_id'])) {
            $job = Job::where('id', $data['job_id'])
                ->where('company_id', $data['company_id'])
                ->where('status', 'open')
                ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'error' => '指定された求人が見つからないか、募集が終了しています。',
                ], 404);
            }
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'channel' => IntakeChannel::MEDIA,
                    'source_id' => 'scout_' . ($data['scout_id'] ?? now()->timestamp . '_' . uniqid()),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                $candidate = $data['candidate'];
                $draft = IntakeCandidateDraft::create([
                    'intake_id' => $intake->id,
                    'name' => $candidate['name'],
                    'email' => $candidate['email'] ?? null,
                    'phone' => $candidate['phone'] ?? null,
                    'job_id' => $job?->id,
                    'profile_data' => array_merge(
                        $candidate['profile'] ?? [],
                        [
                            'profile_url' => $candidate['profile_url'] ?? null,
                            'scout_service' => $data['scout_service'],
                            'scout_id' => $data['scout_id'] ?? null,
                        ]
                    ),
                ]);

                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                ];
            });

            Log::info('スカウト応募を受け付けました', $result);

            return response()->json([
                'success' => true,
                'message' => 'スカウト応募を受け付けました。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('スカウト応募の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '応募の処理中にエラーが発生しました。',
            ], 500);
        }
    }

    /**
     * リファラルAPI - 社員紹介
     *
     * POST /api/intake/referral
     */
    public function referral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'job_id' => ['nullable', 'integer', 'exists:recruitment_jobs,id'],
            'referrer' => ['required', 'array'],
            'referrer.employee_id' => ['nullable', 'string', 'max:255'],
            'referrer.name' => ['required', 'string', 'max:255'],
            'referrer.email' => ['required', 'email', 'max:255'],
            'referrer.department' => ['nullable', 'string', 'max:255'],
            'referrer.relationship' => ['nullable', 'string', 'max:255'], // 友人、元同僚等
            'candidate' => ['required', 'array'],
            'candidate.name' => ['required', 'string', 'max:255'],
            'candidate.email' => ['nullable', 'email', 'max:255'],
            'candidate.phone' => ['nullable', 'string', 'max:50'],
            'candidate.recommendation' => ['nullable', 'string'],
            'candidate.profile' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // job_idが指定されている場合は確認
        $job = null;
        if (!empty($data['job_id'])) {
            $job = Job::where('id', $data['job_id'])
                ->where('company_id', $data['company_id'])
                ->where('status', 'open')
                ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'error' => '指定された求人が見つからないか、募集が終了しています。',
                ], 404);
            }
        }

        try {
            $result = DB::transaction(function () use ($data, $job) {
                $intake = ApplicationIntake::create([
                    'company_id' => $data['company_id'],
                    'channel' => IntakeChannel::REFERRAL,
                    'source_id' => 'referral_' . now()->timestamp . '_' . uniqid(),
                    'raw_data' => $data,
                    'status' => IntakeStatus::RECEIVED,
                    'received_at' => now(),
                ]);

                $candidate = $data['candidate'];
                $referrer = $data['referrer'];

                $draft = IntakeCandidateDraft::create([
                    'intake_id' => $intake->id,
                    'name' => $candidate['name'],
                    'email' => $candidate['email'] ?? null,
                    'phone' => $candidate['phone'] ?? null,
                    'job_id' => $job?->id,
                    'profile_data' => array_merge(
                        $candidate['profile'] ?? [],
                        [
                            'recommendation' => $candidate['recommendation'] ?? null,
                            'referrer_employee_id' => $referrer['employee_id'] ?? null,
                            'referrer_name' => $referrer['name'],
                            'referrer_email' => $referrer['email'],
                            'referrer_department' => $referrer['department'] ?? null,
                            'referrer_relationship' => $referrer['relationship'] ?? null,
                        ]
                    ),
                ]);

                $intake->update(['status' => IntakeStatus::PROCESSING]);

                return [
                    'intake_id' => $intake->id,
                    'draft_id' => $draft->id,
                ];
            });

            Log::info('リファラル応募を受け付けました', $result);

            return response()->json([
                'success' => true,
                'message' => 'リファラル応募を受け付けました。',
                'data' => $result,
            ], 201);

        } catch (\Exception $e) {
            Log::error('リファラル応募の処理に失敗しました', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'error' => '応募の処理中にエラーが発生しました。',
            ], 500);
        }
    }
}
