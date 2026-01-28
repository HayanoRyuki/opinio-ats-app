<?php

namespace App\Http\Controllers;

use App\Application\MyPage\GetTodayActionsUseCase;
use Illuminate\Http\JsonResponse;

final class MyPageController extends Controller
{
    private GetTodayActionsUseCase $useCase;

    public function __construct(GetTodayActionsUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * マイページ（今日のアクション）
     */
    public function index(): JsonResponse
    {
        /**
         * ※ ここで渡している pending 系は
         *   後で Repository / QueryService に置き換える前提
         *   いまは最小構成でOK
         */
        $actions = $this->useCase->execute(
            pendingSchedules: [],
            pendingEvaluations: [],
            waitingReplies: []
        );

        return response()->json([
            'actions' => $actions,
        ]);
    }
}
