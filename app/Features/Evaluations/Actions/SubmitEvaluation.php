<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Enums\EvaluatorType;
use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Exceptions\IncompleteEvaluationException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Users\Models\User;

final class SubmitEvaluation
{
    /**
     * Save a draft, or submit a complete form and refresh the evaluatee's rank.
     *
     * @param  array<string, mixed>  $answers
     *
     * @throws IncompleteEvaluationException
     * @throws EvaluationRuleException
     */
    public function __invoke(
        Evaluation $evaluation,
        User $evaluatee,
        User $evaluator,
        EvaluatorType $type,
        array $answers,
        bool $asDraft,
    ): EvaluationForm {
        if (! $evaluation->isOngoing()) {
            throw new EvaluationRuleException('Forms can only be changed while the evaluation is open.');
        }

        $alreadySubmitted = EvaluationForm::where([
            'evaluation_id' => $evaluation->id,
            'user_id' => $evaluatee->id,
            'evaluator_type' => $type->value,
            'status' => 'submitted',
        ])->exists();

        if ($alreadySubmitted) {
            throw new EvaluationRuleException('This form is already submitted and can no longer be changed.');
        }

        // Keep only this form's questions, so stray keys never count toward the score.
        $answers = array_intersect_key($answers, EvaluationForm::getQuestionsForEvaluator($type->value));

        if ($type === EvaluatorType::Adviser) {
            $evaluation->loadMissing('council.awardType');
            $answers[EvaluationForm::LENGTH_OF_SERVICE_KEY] = EvaluationForm::getLengthOfServiceData($evaluation, $evaluatee->id)['score'];
        }

        if (! $asDraft) {
            $this->assertComplete($type, $answers);
        }

        return EvaluationForm::updateOrCreate(
            [
                'evaluation_id' => $evaluation->id,
                'user_id' => $evaluatee->id,
                'evaluator_type' => $type->value,
                'evaluator_id' => $evaluator->id,
            ],
            [
                'answers' => $answers,
                'status' => $asDraft ? 'draft' : 'submitted',
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    private function assertComplete(EvaluatorType $type, array $answers): void
    {
        foreach (array_keys(EvaluationForm::getQuestionsForEvaluator($type->value)) as $questionKey) {
            if (! isset($answers[$questionKey]) || $answers[$questionKey] === '') {
                throw IncompleteEvaluationException::make();
            }
        }
    }
}
