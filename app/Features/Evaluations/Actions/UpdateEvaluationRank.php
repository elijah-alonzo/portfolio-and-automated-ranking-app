<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Enums\RankTier;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Evaluations\Models\EvaluationRank;

final class UpdateEvaluationRank
{
    public function __construct(private readonly CalculateFinalScore $calculateFinalScore) {}

    public function __invoke(int $evaluationId, int $userId, ?int $councilId): EvaluationRank
    {
        $scores = EvaluationForm::query()
            ->where('evaluation_id', $evaluationId)
            ->where('user_id', $userId)
            ->pluck('evaluator_score', 'evaluator_type')
            ->all();

        $finalScore = ($this->calculateFinalScore)($scores);

        $rank = EvaluationRank::firstOrCreate([
            'evaluation_id' => $evaluationId,
            'user_id' => $userId,
            'council_id' => $councilId,
        ]);

        $rank->update([
            'final_score' => $finalScore,
            'rank' => $finalScore === null ? null : RankTier::fromScore($finalScore),
            'status' => $finalScore === null ? 'pending' : 'finalized',
        ]);

        return $rank;
    }
}
