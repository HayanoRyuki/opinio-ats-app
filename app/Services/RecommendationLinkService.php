<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Recommendation;
use App\Models\RecommendationLink;
use Illuminate\Support\Facades\DB;

class RecommendationLinkService
{
    /**
     * 推薦を候補者に紐付ける
     */
    public function link(Recommendation $recommendation, int $candidateId, ?int $applicationId = null): RecommendationLink
    {
        return DB::transaction(function () use ($recommendation, $candidateId, $applicationId) {
            return RecommendationLink::updateOrCreate(
                [
                    'recommendation_id' => $recommendation->id,
                    'candidate_id' => $candidateId,
                ],
                [
                    'application_id' => $applicationId,
                    'linked_at' => now(),
                ]
            );
        });
    }

    /**
     * 紐付け解除
     */
    public function unlink(Recommendation $recommendation, int $candidateId): bool
    {
        return DB::transaction(function () use ($recommendation, $candidateId) {
            return (bool) RecommendationLink::where('recommendation_id', $recommendation->id)
                ->where('candidate_id', $candidateId)
                ->delete();
        });
    }

    /**
     * 重複推薦を検出
     *
     * 同じエージェントから同じ候補者（名前・メール一致）への推薦がないかチェック
     */
    public function findDuplicateRecommendations(Recommendation $recommendation): array
    {
        $candidateData = $recommendation->candidate_data ?? [];
        $email = $candidateData['email'] ?? null;
        $name = $candidateData['name'] ?? null;

        $duplicates = [];

        if (!$email && !$name) {
            return $duplicates;
        }

        $query = Recommendation::where('company_id', $recommendation->company_id)
            ->where('id', '!=', $recommendation->id);

        // エージェントが同じ推薦を検索
        if ($recommendation->agent_id) {
            $query->where('agent_id', $recommendation->agent_id);
        } elseif ($recommendation->agent_company_name) {
            $query->where('agent_company_name', $recommendation->agent_company_name);
        }

        $candidates = $query->get();

        foreach ($candidates as $existing) {
            $existingData = $existing->candidate_data ?? [];
            $matchType = null;

            if ($email && ($existingData['email'] ?? null) === $email) {
                $matchType = 'email';
            } elseif ($name && ($existingData['name'] ?? null) === $name) {
                $matchType = 'name';
            }

            if ($matchType) {
                $duplicates[] = [
                    'recommendation' => $existing,
                    'match_type' => $matchType,
                ];
            }
        }

        return $duplicates;
    }

    /**
     * 推薦から候補者を検索（紐付け候補の提示用）
     */
    public function searchCandidatesForLinking(Recommendation $recommendation, int $companyId): array
    {
        $candidateData = $recommendation->candidate_data ?? [];
        $email = $candidateData['email'] ?? null;
        $name = $candidateData['name'] ?? null;

        $candidates = [];

        if ($email) {
            $byEmail = Candidate::where('company_id', $companyId)
                ->whereHas('person', fn($q) => $q->where('email', $email))
                ->with('person')
                ->get();

            foreach ($byEmail as $c) {
                $candidates[] = [
                    'candidate' => $c,
                    'match_type' => 'email',
                ];
            }
        }

        if ($name) {
            $byName = Candidate::where('company_id', $companyId)
                ->whereHas('person', fn($q) => $q->where('name', $name))
                ->with('person')
                ->get()
                ->filter(fn($c) => !collect($candidates)->pluck('candidate.id')->contains($c->id));

            foreach ($byName as $c) {
                $candidates[] = [
                    'candidate' => $c,
                    'match_type' => 'name',
                ];
            }
        }

        return $candidates;
    }
}
