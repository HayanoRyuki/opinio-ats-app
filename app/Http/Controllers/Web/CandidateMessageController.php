<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidateMessageController extends Controller
{
    /**
     * チャットメッセージ投稿
     */
    public function store(Request $request, Candidate $candidate)
    {
        $companyId = $request->attributes->get('company_id');
        $authUserId = $request->attributes->get('auth_user_id');
        $user = \App\Models\User::find($authUserId);

        // 自社の候補者のみ
        if ($candidate->company_id !== $companyId) {
            abort(403);
        }

        // 本人チェック：自分が候補者本人ならチャット投稿不可
        if ($user && $user->person_id && $candidate->person_id && $user->person_id === $candidate->person_id) {
            abort(403, 'この候補者のチャットにはアクセスできません');
        }

        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $authUser = Auth::user();

        CandidateMessage::create([
            'candidate_id' => $candidate->id,
            'user_id' => $authUserId,
            'sender_name' => $authUser->name ?? '名前未設定',
            'body' => $request->input('body'),
        ]);

        return redirect()->back();
    }
}
