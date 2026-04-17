<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class EvaluationSubmissionController extends Controller
{
    public function submit(Request $request, $evaluation, $user, $type)
    {
        $evaluation = Evaluation::findOrFail($evaluation);
        $evaluatee = User::findOrFail($user);
        $evaluationType = $type;

        // Permission checks
        $authUser = Auth::user();
        if (!$authUser) {
            abort(401, 'Authentication required.');
        }

        switch ($evaluationType) {
            case 'adviser':
                if ($evaluation->council_adviser_id !== $authUser->id) {
                    abort(403, 'You are not authorized to evaluate as an adviser.');
                }
                break;
            case 'peer':
                if (!EvaluationPeerEvaluator::canEvaluateAsPeer(
                    $evaluation->id,
                    $authUser->id,
                    $evaluatee->id
                )) {
                    abort(403, 'You are not authorized to evaluate this user as a peer.');
                }
                break;
            case 'self':
                if ($evaluatee->id !== $authUser->id) {
                    abort(403, 'You can only perform self-evaluation on your own record.');
                }
                break;
            default:
                abort(404, 'Invalid evaluation type');
        }

        $peerAssignmentId = null;
        if ($evaluationType === 'peer') {
            $peerAssignmentId = EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
                ->where('evaluator_user_id', $authUser->id)
                ->where('evaluatee_user_id', $evaluatee->id)
                ->value('id');

            if (!$peerAssignmentId) {
                abort(403, 'Peer evaluation assignment not found.');
            }
        }

        $answers = $request->input('answers', []);
        $submissionAction = $request->input('submission_action', 'submitted');

        if ($evaluationType === 'adviser') {
            $evaluation->loadMissing('council.awardType');
            $serviceData = EvaluationForm::getLengthOfServiceData($evaluation, $evaluatee->id);
            $answers[EvaluationForm::LENGTH_OF_SERVICE_KEY] = $serviceData['score'];
        }

        if ($submissionAction === 'submitted') {
            $questions = EvaluationForm::getQuestionsForEvaluator($evaluationType);
            foreach (array_keys($questions) as $questionKey) {
                if (!isset($answers[$questionKey]) || $answers[$questionKey] === '') {
                    return back()->with('error', 'All evaluation questions must be answered before submitting.');
                }
            }
        }

        // Save or update the evaluation form and mark as submitted
        $lookup = [
            'evaluation_id' => $evaluation->id,
            'user_id' => $evaluatee->id,
            'evaluator_type' => $evaluationType,
        ];

        if ($evaluationType === 'peer') {
            $lookup['evaluation_peer_evaluator_id'] = $peerAssignmentId;
        }

        EvaluationForm::updateOrCreate(
            $lookup,
            [
                'evaluation_peer_evaluator_id' => $peerAssignmentId,
                'answers' => $answers,
                'status' => $submissionAction === 'draft' ? 'draft' : 'submitted',
            ]
        );

        $admins = User::where('role', 'admin')
            ->whereKeyNot($authUser->id)
            ->get();

        $adviser = $evaluation->adviser;
        $recipients = $admins;

        if ($adviser && $adviser->id !== $authUser->id) {
            $recipients = $recipients->push($adviser);
        }

        if ($recipients->isNotEmpty()) {
            $councilName = $evaluation->council?->name ?? 'Council';
            $evaluationLabel = ucfirst($evaluationType) . ' evaluation';

            Notification::make()
                ->title('Evaluation Submitted')
                ->body("{$authUser->name} submitted a {$evaluationLabel} for {$evaluatee->name} ({$councilName}, {$evaluation->academic_year}).")
                ->success()
                ->sendToDatabase($recipients);
        }

        return redirect(\App\Filament\Resources\MyEvaluations\MyEvaluationResource::getUrl('view', ['record' => $evaluation->id]))
            ->with('success', ucfirst($evaluationType) . ' evaluation submitted successfully!');
    }
}
