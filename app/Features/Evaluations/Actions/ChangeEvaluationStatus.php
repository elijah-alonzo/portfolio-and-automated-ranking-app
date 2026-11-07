<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Users\Models\User;
use Filament\Notifications\Notification;

/**
 * Moves a council evaluation through closed → ongoing → completed (or back to closed while ongoing).
 */
final class ChangeEvaluationStatus
{
    public function __invoke(Evaluation $evaluation, string $to): void
    {
        match ([$evaluation->status, $to]) {
            [Evaluation::STATUS_CLOSED, Evaluation::STATUS_ONGOING] => $this->assertReadyToOpen($evaluation),
            [Evaluation::STATUS_ONGOING, Evaluation::STATUS_CLOSED] => null,
            [Evaluation::STATUS_ONGOING, Evaluation::STATUS_COMPLETED] => $this->assertAllSubmitted($evaluation),
            default => throw new EvaluationRuleException('That step is not available for this evaluation right now.'),
        };

        $evaluation->update(['status' => $to]);

        if ($to !== Evaluation::STATUS_CLOSED) {
            $this->notifyEveryone($evaluation, $to);
        }
    }

    private function assertReadyToOpen(Evaluation $evaluation): void
    {
        $officerIds = $evaluation->positionSlots()->whereNotNull('user_id')->pluck('user_id')->unique();

        if ($officerIds->isEmpty()) {
            throw new EvaluationRuleException('Assign at least one officer before opening the evaluation.');
        }

        $withPeer = EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
            ->whereIn('evaluatee_user_id', $officerIds)
            ->pluck('evaluatee_user_id');

        $missing = $officerIds->diff($withPeer)->count();

        if ($missing > 0) {
            throw new EvaluationRuleException("{$missing} officer(s) still need a peer evaluator.");
        }
    }

    private function assertAllSubmitted(Evaluation $evaluation): void
    {
        if (! $evaluation->areAllFormsSubmitted()) {
            throw new EvaluationRuleException('Every self, peer and adviser form must be submitted before completing.');
        }
    }

    private function notifyEveryone(Evaluation $evaluation, string $status): void
    {
        [$title, $body] = $status === Evaluation::STATUS_ONGOING
            ? ['Evaluation opened', 'An evaluation you are part of is now open.']
            : ['Evaluation completed', 'An evaluation you are part of has been completed.'];

        $recipients = $evaluation->users()->get()
            ->push($evaluation->adviser)
            ->merge(User::where('role', 'admin')->get())
            ->filter()
            ->unique('id');

        foreach ($recipients as $user) {
            Notification::make()->title($title)->body($body)->success()->sendToDatabase($user);
        }
    }
}
