<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Enums\EvaluatorType;
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
     */
    public function __invoke(
        Evaluation $evaluation,
        User $evaluatee,
        User $evaluator,
        EvaluatorType $type,
        array $answers,
        bool $asDraft,
    ): EvaluationForm {
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
