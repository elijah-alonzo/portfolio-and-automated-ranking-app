<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 * Handles evaluation form submissions and status updates.
 */
class EvaluationSubmissionController extends Controller
{
    /**
     * Submit an evaluation form and mark as submitted.
     *
     * @param Request $request
     * @param int $evaluation
     * @param int $user
     * @param string $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request, $evaluation, $user, $type)
    {
        $evaluation = Evaluation::findOrFail($evaluation);
        $evaluatee = User::findOrFail($user);
        $evaluationType = $type;

        // Permission checks
        $authUser = Auth::user();
        switch ($evaluationType) {
            case 'adviser':
                if ($evaluation->council_adviser_id !== $authUser->id) {
                    abort(403, 'You are not authorized to evaluate as an adviser.');
                }
                break;
            case 'peer':
                // Add peer permission logic if needed
                break;
            case 'self':
                if ($evaluatee->id !== $authUser->id) {
                    abort(403, 'You can only perform self-evaluation on your own record.');
                }
                break;
            default:
                abort(404, 'Invalid evaluation type');
        }

        $answers = $request->input('answers', []);
        $questions = EvaluationForm::getQuestionsForEvaluator($evaluationType);
        foreach (array_keys($questions) as $questionKey) {
            if (!isset($answers[$questionKey]) || $answers[$questionKey] === '') {
                return back()->with('error', 'All evaluation questions must be answered before submitting.');
            }
        }

        // Save or update the evaluation form and mark as submitted
        EvaluationForm::updateOrCreate(
            [
                'evaluation_id' => $evaluation->id,
                'user_id' => $evaluatee->id,
                'evaluator_type' => $evaluationType,
                'evaluator_id' => $evaluationType === 'peer' ? $authUser->id : null,
            ],
            [
                'answers' => $answers,
                'status' => 'submitted',
            ]
        );

        return redirect(\App\Filament\Resources\MyEvaluations\MyEvaluationResource::getUrl('view', ['record' => $evaluation->id]))
            ->with('success', ucfirst($evaluationType) . ' evaluation submitted successfully!');
    }
}
