<?php

namespace App\Features\Evaluations\Queries;

use App\Features\Evaluations\Enums\EvaluatorType;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Evaluations\Models\EvaluationPositionSlot;
use App\Features\Evaluations\Models\EvaluationRank;
use App\Features\Positions\Models\Position;
use App\Features\Users\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Everything the council page shows: each position slot, who holds it, their peer evaluator,
 * where each of their three forms stands, and which forms the viewer may open.
 */
final class CouncilBoard
{
    /**
     * @return list<array<string, mixed>>
     */
    public function officers(Evaluation $evaluation, User $viewer): array
    {
        $statuses = EvaluationForm::where('evaluation_id', $evaluation->id)
            ->get(['user_id', 'evaluator_type', 'status'])
            ->mapWithKeys(fn (EvaluationForm $form) => ["{$form->user_id}|{$form->evaluator_type}" => $form->status]);

        $peers = EvaluationPeerEvaluator::where('evaluation_id', $evaluation->id)
            ->with('evaluatorUser:id,name')
            ->get()
            ->keyBy('evaluatee_user_id');

        $ranks = EvaluationRank::where('evaluation_id', $evaluation->id)->get()->keyBy('user_id');

        $slots = EvaluationPositionSlot::where('evaluation_id', $evaluation->id)
            ->with(['position', 'user.department'])
            ->orderBy(Position::select('hierarchy')->whereColumn('positions.id', 'evaluation_position_slots.position_id'))
            ->orderBy('slot_number')
            ->get();

        // While open or done, empty slots are noise; while closed the adviser needs them to assign people.
        if (! $evaluation->isClosed()) {
            $slots = $slots->whereNotNull('user_id')->values();
        }

        return $slots->map(function (EvaluationPositionSlot $slot) use ($evaluation, $viewer, $statuses, $peers, $ranks) {
            $student = $slot->user;
            $peer = $student ? $peers->get($student->id)?->evaluatorUser : null;
            $rank = $student ? $ranks->get($student->id) : null;

            return [
                'id' => $slot->id,
                'position' => $slot->position->title,
                'recommendation' => $this->recommendation($slot->position?->branch),
                'student' => $student ? [
                    'id' => $student->id,
                    'name' => $student->name,
                    'avatar' => $student->avatarUrl(),
                    'department' => $student->department?->name,
                ] : null,
                'peer' => $peer ? ['id' => $peer->id, 'name' => $peer->name] : null,
                'forms' => $student ? collect(EvaluatorType::cases())->mapWithKeys(fn (EvaluatorType $type) => [
                    $type->value => $this->formState($evaluation, $viewer, $student, $type, $statuses->get("{$student->id}|{$type->value}", 'pending')),
                ])->all() : null,
                'score' => $rank?->final_score !== null ? [
                    'final' => (float) $rank->final_score,
                    'rank' => $rank->rank?->label(),
                ] : null,
            ];
        })->all();
    }

    /**
     * @return array{status: string, canOpen: bool, isMine: bool}
     */
    private function formState(Evaluation $evaluation, User $viewer, User $student, EvaluatorType $type, string $status): array
    {
        $isMine = Gate::forUser($viewer)->allows('fill', [$evaluation, $student, $type]);
        $canRead = $isMine || Gate::forUser($viewer)->allows('read', [$evaluation, $student, $type]);

        return [
            'status' => $status,
            'isMine' => $isMine,
            // Your own form opens once the evaluation is open; anyone else's only after it has something in it.
            'canOpen' => $canRead && ($isMine ? ! $evaluation->isClosed() : $status !== 'pending'),
        ];
    }

    private function recommendation(?string $branch): ?string
    {
        return match ($branch) {
            'Executive' => 'Executive: 3.00 to 2.41',
            'Legislative' => 'Legislative: 2.40 to 1.81',
            'Judiciary', 'Mayoral' => 'Judicial or Mayoral: 1.80 to 1.21',
            default => null,
        };
    }
}
