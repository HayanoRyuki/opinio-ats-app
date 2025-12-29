<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 面接官は閲覧不可（×）
        if ($user->role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        // 管理者、採用担当は閲覧可（◎/○）
        return view('applications.index');
    }

    public function show($id)
    {
        $user = auth()->user();

        // 面接官は担当の案件のみ閲覧可能（△）
        if ($user->role === 'interviewer' && ! $this->isAssigned($user, $id)) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('applications.show', ['id' => $id]);
    }

    private function isAssigned($user, $applicationId)
    {
        // TODO: 実際の担当判定ロジックをここに実装
        return true; // 仮置き
    }
}
