<?php

namespace App\Http\Controllers\Decide;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HiringDecisionController extends Controller
{
    /**
     * 採用判断を保存する（Decide Domain）
     *
     * ※ ここではまだ DB 保存しない
     * ※ まずはルーティングと submit が成立することを確認する
     */
    public function store(Request $request, int $application)
    {
        $request->validate([
            'decision' => 'required|in:hire,reject,hold',
            'reason'   => 'nullable|string',
        ]);

        // いったん何もしない（次ステップで UseCase を呼ぶ）
        // dump($application, $request->all());

        return back()->with('status', '採用判断（仮）を受け取りました');
    }
}
