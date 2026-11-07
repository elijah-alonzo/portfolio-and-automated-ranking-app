<?php

namespace App\Features\Evaluations\Controllers;

use App\Features\Evaluations\Actions\SubmitEvaluation;
use App\Features\Evaluations\Enums\EvaluatorType;
use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Exceptions\IncompleteEvaluationException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Evaluations\Requests\SubmitEvaluationRequest;
use App\Features\Users\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * One self, peer or adviser form: the rubric, the saved answers, and whether it can still change.
 */
final class EvaluationFormController
{
    public function show(Request $request, Evaluation $evaluation, User $user, EvaluatorType $type): Response
    {
        Gate::authorize('read', [$evaluation, $user, $type]);

        $evaluation->load('council.awardType');

        $form = EvaluationForm::where([
            'evaluation_id' => $evaluation->id,
            'user_id' => $user->id,
            'evaluator_type' => $type->value,
        ])->first();

        $status = $form->status ?? 'pending';
        $canFill = Gate::allows('fill', [$evaluation, $user, $type]);
        $service = $type === EvaluatorType::Adviser ? EvaluationForm::getLengthOfServiceData($evaluation, $user->id) : null;

        return Inertia::render('app/EvaluationForm', [
            'council' => [
                'id' => $evaluation->id,
                'name' => $evaluation->council->name,
                'academicYear' => (string) $evaluation->academic_year,
            ],
            'evaluatee' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatarUrl(),
                'position' => $evaluation->users()->whereKey($user->id)->value('evaluation_user.position'),
            ],
            'type' => $type->value,
            'weight' => $type->weight(),
            'status' => $status,
            'isEditable' => $canFill && $evaluation->isOngoing() && $status !== 'submitted',
            'canExport' => $form !== null && Gate::allows('manage', $evaluation),
            'domains' => $this->domains($type),
            'answers' => (object) ($form->answers ?? []),
            'lengthOfService' => $service ? [
                'key' => EvaluationForm::LENGTH_OF_SERVICE_KEY,
                'years' => $service['years'],
                'score' => $service['score'],
                'awardType' => $service['award_type'],
            ] : null,
        ]);
    }

    public function store(SubmitEvaluationRequest $request, Evaluation $evaluation, User $user, EvaluatorType $type, SubmitEvaluation $submit): RedirectResponse
    {
        try {
            $submit($evaluation, $user, $request->user(), $type, $request->answers(), $request->isDraft());
        } catch (IncompleteEvaluationException|EvaluationRuleException $e) {
            Inertia::flash('error', $e->getMessage());

            return back();
        }

        if ($request->isDraft()) {
            Inertia::flash('success', 'Draft saved. You can come back and finish it later.');

            return back();
        }

        if ($evaluation->adviser && ! $evaluation->adviser->is($request->user())) {
            Notification::make()
                ->title('Evaluation submitted')
                ->body("{$request->user()->name} submitted a {$type->value} evaluation for {$user->name}.")
                ->success()
                ->sendToDatabase($evaluation->adviser);
        }

        Inertia::flash('success', ucfirst($type->value).' evaluation for '.$user->name.' submitted.');

        return redirect()->route('councils.show', $evaluation);
    }

    /**
     * The rubric for this form, grouped as domain → strand → question.
     *
     * @return list<array<string, mixed>>
     */
    private function domains(EvaluatorType $type): array
    {
        return collect(EvaluationForm::getQuestionsForEvaluator($type->value))
            ->map(fn (array $question, string $key) => [...$question, 'key' => $key])
            ->groupBy('domain_key')
            ->map(fn ($questions, string $domainKey) => [
                'key' => $domainKey,
                'title' => $questions->first()['domain'],
                'description' => $questions->first()['domain_description'],
                'strands' => $questions->groupBy('strand_key')->map(fn ($strandQuestions) => [
                    'title' => $strandQuestions->first()['strand'],
                    'questions' => $strandQuestions->map(fn (array $question) => [
                        'key' => $question['key'],
                        'number' => $question['qkey'],
                        'text' => $question['text'],
                        'criteria' => collect($question['criteria'])
                            ->map(fn (string $label, int $score) => ['score' => $score, 'label' => $label])
                            ->values()
                            ->all(),
                    ])->values()->all(),
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }
}
