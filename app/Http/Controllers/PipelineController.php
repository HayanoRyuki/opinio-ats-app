<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index(Request $request)
    {
        // VerifyJwt middleware で積まれた role を取得
        $role = $request->attributes->get('role');

        // 面接官は担当のパイプラインのみ閲覧可能（△）
        if ($role === 'interviewer' && ! $this->hasAssignedPipeline()) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('pipeline.index');
    }

    public function show(Request $request, $id)
    {
        // VerifyJwt middleware で積まれた role を取得
        $role = $request->attributes->get('role');

        // 面接官は担当のパイプラインのみ閲覧可能（△）
        if ($role === 'interviewer' && ! $this->isAssigned($id)) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('pipeline.show', ['id' => $id]);
    }

    private function hasAssignedPipeline()
    {
        // TODO: 担当判定ロジック
        return true; // 仮置き（全許可）
    }

    private function isAssigned($pipelineId)
    {
        // TODO: 担当判定ロジック
        return true; // 仮置き（全許可）
    }
}
