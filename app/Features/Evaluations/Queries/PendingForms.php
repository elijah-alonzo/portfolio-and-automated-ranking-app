<?php

namespace App\Features\Evaluations\Queries;

use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Users\Models\User;

/**
 * The forms waiting on a person across every open evaluation: their own self evaluation,
 * the peers they were assigned, and, for advisers, each officer in the councils they advise.
 */
final class PendingForms
{
    /**
     * @return list<array{evaluationId: int, council: string, academicYear: string, evaluateeId: int, evaluatee: string, evaluateeAvatar: ?string, type: string, status: string}>
     */
    public function for(User $user): array
    {
        $evaluations = Evaluation::query()
            ->where('status', Evaluation::STATUS_ONGOING)
            ->where(fn ($query) => $query
                ->where('council_adviser_id', $user->id)
                ->orWhereHas('users', fn ($officers) => $officers->whereKey($user->id))
                ->orWhereHas('peerEvaluators', fn ($peers) => $peers->where('evaluator_user_id', $user->id)))
            ->with(['council', 'users'])
            ->get();

        $tasks = [];

        foreach ($evaluations as $evaluation) {
            $statuses = EvaluationForm::where('evaluation_id', $evaluation->id)
                ->get(['user_id', 'evaluator_type', 'status'])
                ->mapWithKeys(fn (EvaluationForm $form) => ["{$form->user_id}|{$form->evaluator_type}" => $form->status]);

            $add = function (User $evaluatee, string $type) use (&$tasks, $evaluation, $statuses): void {
                $status = $statuses->get("{$evaluatee->id}|{$type}", 'pending');

                if ($status === 'submitted') {
                    return;
                }

                $tasks[] = [
                    'evaluationId' => $evaluation->id,
                    'council' => $evaluation->council->name,
                    'academicYear' => (string) $evaluation->academic_year,
                    'evaluateeId' => $evaluatee->id,
                    'evaluatee' => $evaluatee->name,
                    'evaluateeAvatar' => $evaluatee->avatarUrl(),
                    'type' => $type,
                    'status' => $status,
                ];
            };

            if ($evaluation->users->contains($user)) {
                $add($user, 'self');
            }

            EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
                ->where('evaluator_user_id', $user->id)
                ->with('evaluateeUser')
                ->get()
                ->each(fn (EvaluationPeerEvaluator $pairing) => $pairing->evaluateeUser && $add($pairing->evaluateeUser, 'peer'));

            if ($evaluation->council_adviser_id === $user->id) {
                $evaluation->users->each(fn (User $officer) => $add($officer, 'adviser'));
            }
        }

        return $tasks;
    }
}
