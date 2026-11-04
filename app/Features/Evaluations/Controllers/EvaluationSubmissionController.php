<?php

namespace App\Features\Evaluations\Controllers;

use App\Features\Evaluations\Actions\SubmitEvaluation;
use App\Features\Evaluations\Enums\EvaluatorType;
use App\Features\Evaluations\Exceptions\IncompleteEvaluationException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Requests\SubmitEvaluationRequest;
use App\Features\Users\Models\User;
use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Http\Controllers\Controller;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;

final class EvaluationSubmissionController extends Controller
{
    public function submit(
        SubmitEvaluationRequest $request,
        SubmitEvaluation $submitEvaluation,
        Evaluation $evaluation,
        User $user,
        EvaluatorType $type,
    ): RedirectResponse {
        try {
            $submitEvaluation($evaluation, $user, $request->user(), $type, $request->answers(), $request->isDraft());
        } catch (IncompleteEvaluationException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($request->isDraft()) {
            return back()->with('success', 'Draft saved successfully.');
        }

        if ($evaluation->adviser) {
            Notification::make()
                ->title('Evaluation Submitted')
                ->body(($user->name ?? 'A student').' submitted a '.$type->value.' evaluation.')
                ->success()
                ->sendToDatabase($evaluation->adviser);
        }

        return redirect(MyEvaluationResource::getUrl('view', ['record' => $evaluation->id]))
            ->with('success', ucfirst($type->value).' evaluation submitted successfully!');
    }
}
