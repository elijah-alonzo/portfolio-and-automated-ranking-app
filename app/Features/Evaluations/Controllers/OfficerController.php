<?php

namespace App\Features\Evaluations\Controllers;

use App\Features\Evaluations\Actions\AssignPeerEvaluator;
use App\Features\Evaluations\Actions\AssignSlotStudent;
use App\Features\Evaluations\Actions\RemoveSlotStudent;
use App\Features\Evaluations\Actions\SendEvaluationReminders;
use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationPositionSlot;
use App\Features\Users\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

/**
 * The adviser's officer controls on a council page.
 */
final class OfficerController
{
    public function assign(Request $request, Evaluation $evaluation, EvaluationPositionSlot $positionSlot, AssignSlotStudent $assign): RedirectResponse
    {
        Gate::authorize('manage', $evaluation);

        $student = User::findOrFail($request->validate(['student_id' => ['required', 'integer']])['student_id']);

        return $this->attempt(fn () => $assign($positionSlot, $student), "{$student->name} is now {$positionSlot->position?->title}.");
    }

    public function remove(Evaluation $evaluation, EvaluationPositionSlot $positionSlot, RemoveSlotStudent $remove): RedirectResponse
    {
        Gate::authorize('manage', $evaluation);

        return $this->attempt(fn () => $remove($positionSlot), 'The position is empty again.');
    }

    public function peer(Request $request, Evaluation $evaluation, User $officer, AssignPeerEvaluator $assign): RedirectResponse
    {
        Gate::authorize('manage', $evaluation);

        $evaluator = User::findOrFail($request->validate(['evaluator_id' => ['required', 'integer']])['evaluator_id']);

        return $this->attempt(
            fn () => $assign($evaluation, $officer, $evaluator, $request->user()),
            "{$evaluator->name} will evaluate {$officer->name}.",
        );
    }

    public function remind(Evaluation $evaluation, User $officer, SendEvaluationReminders $remind): RedirectResponse
    {
        Gate::authorize('manage', $evaluation);

        try {
            $sent = $remind($evaluation, $officer);
        } catch (EvaluationRuleException $e) {
            Inertia::flash('error', $e->getMessage());

            return back();
        }

        Inertia::flash('success', $sent > 0
            ? "Sent {$sent} reminder email(s) about {$officer->name}."
            : "Nothing to remind: {$officer->name}'s self and peer forms are in.");

        return back();
    }

    private function attempt(Closure $action, string $success): RedirectResponse
    {
        try {
            $action();
        } catch (EvaluationRuleException $e) {
            Inertia::flash('error', $e->getMessage());

            return back();
        }

        Inertia::flash('success', $success);

        return back();
    }
}
