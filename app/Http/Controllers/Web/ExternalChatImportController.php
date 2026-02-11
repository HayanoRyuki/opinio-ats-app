<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ExternalChatImport;
use App\Services\ChatSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalChatImportController extends Controller
{
    /**
     * 外部チャット取り込み
     */
    public function store(Request $request, Candidate $candidate, ChatSummaryService $summaryService)
    {
        $companyId = $request->attributes->get('company_id');
        $authUserId = $request->attributes->get('auth_user_id');

        // 自社の候補者のみ
        if ($candidate->company_id !== $companyId) {
            abort(403);
        }

        $request->validate([
            'source' => 'required|string|in:bizreach,wantedly,other',
            'source_label' => 'nullable|string|max:100',
            'raw_text' => 'required|string|max:50000',
        ]);

        $authUser = Auth::user();

        // Claude APIで要約生成
        $summary = $summaryService->summarize(
            $request->input('raw_text'),
            $candidate->name,
            $request->input('source')
        );

        ExternalChatImport::create([
            'candidate_id' => $candidate->id,
            'source' => $request->input('source'),
            'source_label' => $request->input('source_label'),
            'raw_text' => $request->input('raw_text'),
            'summary' => $summary,
            'imported_by' => $authUserId,
            'sender_name' => $authUser->name ?? '名前未設定',
        ]);

        return redirect()->back();
    }
}
