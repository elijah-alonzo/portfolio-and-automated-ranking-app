<?php

namespace App\Features\Evaluations\Controllers;

use App\Features\Evaluations\Actions\ChangeEvaluationStatus;
use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\Evaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

final class CouncilStatusController
{
    public function __invoke(Request $request, Evaluation $evaluation, ChangeEvaluationStatus $change): RedirectResponse
    {
        Gate::authorize('manage', $evaluation);

        $to = $request->validate([
            'status' => ['required', Rule::in([Evaluation::STATUS_CLOSED, Evaluation::STATUS_ONGOING, Evaluation::STATUS_COMPLETED])],
        ])['status'];

        try {
            $change($evaluation, $to);
        } catch (EvaluationRuleException $e) {
            Inertia::flash('error', $e->getMessage());

            return back();
        }

        Inertia::flash('success', match ($to) {
            Evaluation::STATUS_ONGOING => 'The evaluation is open. Officers can start their forms.',
            Evaluation::STATUS_COMPLETED => 'The evaluation is completed and its forms are now read-only.',
            default => 'The evaluation is closed. You can change officers and peers again.',
        });

        return back();
    }
}
