<?php

namespace App\Infrastructure\Eloquent\Repository;

use App\Domain\Repository\CandidateRepository;
use App\Domain\Evaluate\Candidate;
use App\Models\Candidate as CandidateModel;

final class EloquentCandidateRepository implements CandidateRepository
{
    public function all(): array
    {
        return CandidateModel::all()
            ->map(fn (CandidateModel $model) => $this->toDomain($model))
            ->all();
    }

    public function findById(string $candidateId): ?Candidate
    {
        $model = CandidateModel::find($candidateId);

        return $model ? $this->toDomain($model) : null;
    }

    public function save(Candidate $candidate): void
    {
        CandidateModel::updateOrCreate(
            ['id' => $candidate->id()],
            [
                'name' => $candidate->name(),
                // profile / documents は後続で対応
            ]
        );
    }

    private function toDomain(CandidateModel $model): Candidate
    {
        return Candidate::create(
            (string) $model->id,
            $model->name,
            $model->profile ?? [],
            $model->documents ?? []
        );
    }
}
