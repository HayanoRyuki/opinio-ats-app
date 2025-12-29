<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 面接官は担当のパイプラインのみ閲覧可能（△）
        if ($user->role === 'interviewer' && ! $this->hasAssignedPipeline($user)) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('pipeline.index');
    }

    public function show($id)
    {
        $user = auth()->user();

        // 面接官は担当のパイプラインのみ閲覧可能（△）
        if ($user->role === 'interviewer' && ! $this->isAssigned($user, $id)) {
            abort(403, 'アクセス権限がありません。');
        }

        return view('pipeline.show', ['id' => $id]);
    }

    private function hasAssignedPipeline($user)
    {
        // TODO: 担当判定ロジック
        return true;
    }

    private function isAssigned($user, $pipelineId)
    {
        // TODO: 担当判定ロジック
        return true;
    }
}
