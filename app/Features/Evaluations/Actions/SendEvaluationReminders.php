<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Users\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Emails the officer and their peer evaluator about any self or peer form still not submitted.
 */
final class SendEvaluationReminders
{
    /**
     * @return int How many reminders went out.
     */
    public function __invoke(Evaluation $evaluation, User $officer): int
    {
        if (! $evaluation->isOngoing()) {
            throw new EvaluationRuleException('Reminders can only be sent while the evaluation is open.');
        }

        $title = trim($evaluation->council->name.' '.$evaluation->academic_year);
        $sent = 0;

        foreach ($this->pending($evaluation, $officer) as [$recipient, $type]) {
            if (blank($recipient->email)) {
                continue;
            }

            Mail::raw(
                "Hello {$recipient->name},\n\nYou have a pending {$type} evaluation for {$officer->name} in {$title}.\n\nPlease submit it as soon as possible.",
                fn ($mail) => $mail->to($recipient->email)->subject("Pending {$type} evaluation: {$title}"),
            );

            $sent++;
        }

        return $sent;
    }

    /**
     * @return list<array{0: User, 1: string}>
     */
    private function pending(Evaluation $evaluation, User $officer): array
    {
        $submitted = EvaluationForm::where('evaluation_id', $evaluation->id)
            ->where('user_id', $officer->id)
            ->where('status', 'submitted')
            ->pluck('evaluator_type');

        $pending = [];

        if (! $submitted->contains('self')) {
            $pending[] = [$officer, 'self'];
        }

        $peer = EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
            ->where('evaluatee_user_id', $officer->id)
            ->first()?->evaluatorUser;

        if ($peer && ! $submitted->contains('peer')) {
            $pending[] = [$peer, 'peer'];
        }

        return $pending;
    }
}
