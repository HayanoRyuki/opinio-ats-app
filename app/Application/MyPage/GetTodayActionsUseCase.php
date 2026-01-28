<?php

namespace App\Application\MyPage;

use App\Domain\Evaluate\Candidate;
use App\Domain\Record\HiringDecision;

/**
 * GetTodayActionsUseCase
 *
 * マイページ表示用に、
 * 今日対応すべきアクションを抽出するユースケース。
 */
final class GetTodayActionsUseCase
{
    /**
     * 今日のアクション一覧を取得
     *
     * @param Candidate[] $candidates
     * @param array $pendingSchedules 日程調整待ちID一覧
     * @param array $pendingEvaluations 評価未入力ID一覧
     * @param array $waitingReplies 返信待ちID一覧
     */
    public function execute(
        array $candidates,
        array $pendingSchedules,
        array $pendingEvaluations,
        array $waitingReplies
    ): array {
        $actions = [];

        foreach ($candidates as $candidate) {
            $candidateId = $candidate->id();

            if (in_array($candidateId, $pendingSchedules, true)) {
                $actions[] = [
                    'type' => 'schedule',
                    'candidate_id' => $candidateId,
                    'message' => '日程調整が未完了です',
                ];
            }

            if (in_array($candidateId, $pendingEvaluations, true)) {
                $actions[] = [
                    'type' => 'evaluation',
                    'candidate_id' => $candidateId,
                    'message' => '評価が未入力です',
                ];
            }

            if (in_array($candidateId, $waitingReplies, true)) {
                $actions[] = [
                    'type' => 'reply',
                    'candidate_id' => $candidateId,
                    'message' => '候補者からの返信待ちです',
                ];
            }
        }

        return $actions;
    }
}
