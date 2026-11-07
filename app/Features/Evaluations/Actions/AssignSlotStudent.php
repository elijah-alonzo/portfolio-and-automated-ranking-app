<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\EvaluationPositionSlot;
use App\Features\Users\Models\User;
use Filament\Notifications\Notification;

/**
 * Puts a student in a council position slot while the evaluation is closed.
 */
final class AssignSlotStudent
{
    public function __construct(private readonly RemoveSlotStudent $removeSlotStudent) {}

    public function __invoke(EvaluationPositionSlot $slot, User $student): void
    {
        $evaluation = $slot->evaluation;

        if (! $evaluation->isClosed()) {
            throw new EvaluationRuleException('Close the evaluation before changing its officers.');
        }

        if (! in_array($student->id, $this->eligibleStudentIds($slot), true)) {
            throw new EvaluationRuleException('That student cannot take this position.');
        }

        if ($slot->user_id && $slot->user_id !== $student->id) {
            ($this->removeSlotStudent)($slot);
        }

        $slot->update(['user_id' => $student->id]);

        $evaluation->users()->syncWithoutDetaching([
            $student->id => ['position' => $slot->position?->title],
        ]);

        Notification::make()
            ->title('Evaluation assignment')
            ->body('You have been added to '.$evaluation->council->name.' '.$evaluation->academic_year.'.')
            ->success()
            ->sendToDatabase($student);
    }

    /**
     * Students from the council's departments who don't already hold another slot in this evaluation.
     *
     * @return list<int>
     */
    public function eligibleStudentIds(EvaluationPositionSlot $slot): array
    {
        $evaluation = $slot->evaluation;
        $departmentIds = $evaluation->council?->departments()->pluck('departments.id')->all() ?? [];

        $taken = EvaluationPositionSlot::where('evaluation_id', $evaluation->id)
            ->whereNotNull('user_id')
            ->where('id', '!=', $slot->id)
            ->pluck('user_id');

        return User::where('role', 'student')
            ->when($departmentIds, fn ($query) => $query->whereIn('department_id', $departmentIds))
            ->whereNotIn('id', $taken)
            ->pluck('id')
            ->all();
    }
}
