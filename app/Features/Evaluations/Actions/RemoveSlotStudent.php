<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Evaluations\Models\EvaluationPositionSlot;

/**
 * Empties a position slot and drops the student's peer pairings in this evaluation.
 */
final class RemoveSlotStudent
{
    public function __invoke(EvaluationPositionSlot $slot): void
    {
        $evaluation = $slot->evaluation;
        $studentId = $slot->user_id;

        if (! $evaluation->isClosed()) {
            throw new EvaluationRuleException('Close the evaluation before changing its officers.');
        }

        if (! $studentId) {
            return;
        }

        $slot->update(['user_id' => null]);

        $stillHoldsASlot = EvaluationPositionSlot::where('evaluation_id', $evaluation->id)
            ->where('user_id', $studentId)
            ->exists();

        if (! $stillHoldsASlot) {
            $evaluation->users()->detach($studentId);
        }

        EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
            ->where(fn ($query) => $query->where('evaluatee_user_id', $studentId)->orWhere('evaluator_user_id', $studentId))
            ->delete();
    }
}
