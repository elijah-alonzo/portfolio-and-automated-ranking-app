<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Users\Models\User;

/**
 * Pairs an officer with the fellow officer who will write their peer evaluation.
 */
final class AssignPeerEvaluator
{
    public function __invoke(Evaluation $evaluation, User $evaluatee, User $evaluator, User $assignedBy): void
    {
        if (! $evaluation->isClosed()) {
            throw new EvaluationRuleException('Close the evaluation before changing peer evaluators.');
        }

        if ($evaluatee->is($evaluator)) {
            throw new EvaluationRuleException('An officer cannot be their own peer evaluator.');
        }

        $officerIds = $evaluation->positionSlots()->whereNotNull('user_id')->pluck('user_id');

        if (! $officerIds->contains($evaluatee->id) || ! $officerIds->contains($evaluator->id)) {
            throw new EvaluationRuleException('Both people must be officers in this evaluation.');
        }

        EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
            ->where('evaluatee_user_id', $evaluatee->id)
            ->delete();

        EvaluationPeerEvaluator::create([
            'evaluation_id' => $evaluation->id,
            'evaluatee_user_id' => $evaluatee->id,
            'evaluator_user_id' => $evaluator->id,
            'assigned_by_user_id' => $assignedBy->id,
            'assigned_at' => now(),
        ]);
    }
}
