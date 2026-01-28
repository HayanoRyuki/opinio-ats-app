<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        // VerifyJwt middleware で積まれた role を取得
        $role = $request->attributes->get('role');

        // 面接官は担当以外アクセス不可（※今は仮で全許可）
        if ($role === 'interviewer' && ! $this->hasAssignedInterviews()) {
            abort(403, 'アクセス権限がありません。');
        }

        // 管理者・採用担当はアクセス可（◎/○）
        return view('interviews.index');
    }

    public function show(Request $request, $id)
    {
        $role = $request->attributes->get('role');

        // 面接官は担当の面接のみ閲覧可（※今は仮で全許可）
        if ($role === 'interviewer' && ! $this->isAssigned($id)) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('interviews.show', ['id' => $id]);
    }

    private function hasAssignedInterviews()
    {
        // TODO: 担当判定ロジック（JWT user_id 等を使う）
        return true; // 仮置き
    }

    private function isAssigned($interviewId)
    {
        // TODO: 担当判定ロジック
        return true; // 仮置き
    }
}
