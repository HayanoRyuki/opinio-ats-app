<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        // VerifyJwt middleware から渡される role
        $role = $request->attributes->get('role');

        // 面接官は閲覧不可（×）
        if ($role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        // 管理者・採用担当は閲覧可（◎/○）
        return view('candidates.index');
    }

    public function show(Request $request, $id)
    {
        // JWT 由来の情報
        $role   = $request->attributes->get('role');
        $userId = $request->attributes->get('user_id');

        // 面接官は担当の候補者のみ閲覧可能（△）
        if ($role === 'interviewer' && ! $this->isAssigned($userId, $id)) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('candidates.show', ['id' => $id]);
    }

    private function isAssigned($userId, $candidateId)
    {
        // TODO: 実際の担当判定ロジックをここに実装
        return true; // 仮置き
    }
}
