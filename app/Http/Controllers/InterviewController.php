<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 面接官は担当以外アクセス不可（△）
        if ($user->role === 'interviewer' && ! $this->hasAssignedInterviews($user)) {
            abort(403, 'アクセス権限がありません。');
        }

        // 管理者・採用担当はアクセス可（◎/○）
        return view('interviews.index');
    }

    public function show($id)
    {
        $user = auth()->user();

        // 面接官は担当の面接のみ閲覧可（△）
        if ($user->role === 'interviewer' && ! $this->isAssigned($user, $id)) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('interviews.show', ['id' => $id]);
    }

    private function hasAssignedInterviews($user)
    {
        // TODO: 担当判定ロジック
        return true; // 仮置き
    }

    private function isAssigned($user, $interviewId)
    {
        // TODO: 担当判定ロジック
        return true; // 仮置き
    }
}
