<?php

namespace App\Features\Evaluations\Policies;

use App\Features\Evaluations\Enums\EvaluatorType;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Users\Models\User;

/**
 * Who may see a council evaluation, run it, and fill or read its forms.
 */
final class EvaluationPolicy
{
    /**
     * The council's adviser and its student officers can open it.
     */
    public function view(User $user, Evaluation $evaluation): bool
    {
        return $this->isAdviser($user, $evaluation) || $this->isOfficer($user, $evaluation);
    }

    /**
     * Only the council's adviser assigns officers, opens, closes and completes the evaluation.
     */
    public function manage(User $user, Evaluation $evaluation): bool
    {
        return $this->isAdviser($user, $evaluation);
    }

    /**
     * Whether this user is the one who fills the given form: the adviser for adviser forms,
     * the officer for their own self form, and the assigned peer for a peer form.
     */
    public function fill(User $user, Evaluation $evaluation, User $evaluatee, EvaluatorType $type): bool
    {
        return match ($type) {
            EvaluatorType::Adviser => $this->isAdviser($user, $evaluation) && $this->isOfficer($evaluatee, $evaluation),
            EvaluatorType::Self => $user->is($evaluatee) && $this->isOfficer($user, $evaluation),
            EvaluatorType::Peer => EvaluationPeerEvaluator::canEvaluateAsPeer($evaluation->id, $user->id, $evaluatee->id),
        };
    }

    /**
     * The filler can always reopen their form; the adviser can read every form in the council.
     */
    public function read(User $user, Evaluation $evaluation, User $evaluatee, EvaluatorType $type): bool
    {
        return $this->fill($user, $evaluation, $evaluatee, $type) || $this->isAdviser($user, $evaluation);
    }

    private function isAdviser(User $user, Evaluation $evaluation): bool
    {
        return $evaluation->council_adviser_id === $user->id;
    }

    private function isOfficer(User $user, Evaluation $evaluation): bool
    {
        return $evaluation->users()->whereKey($user->id)->exists();
    }
}
