<?php

namespace App\Features\Awards\Actions;

use App\Features\Awards\Models\AwardType;
use App\Features\Awards\Models\LeadershipAwardApplication;
use App\Features\Evaluations\Exceptions\EvaluationRuleException;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Users\Models\User;
use Filament\Notifications\Notification;

/**
 * A graduating officer applies for a leadership award once all their council evaluations are complete.
 */
final class ApplyForLeadershipAward
{
    public function __invoke(User $student, AwardType $awardType): LeadershipAwardApplication
    {
        if ($problem = $this->whyNot($student)) {
            throw new EvaluationRuleException($problem);
        }

        $application = LeadershipAwardApplication::create([
            'user_id' => $student->id,
            'award_type_id' => $awardType->id,
            'status' => 'pending',
        ]);

        foreach (User::where('role', 'admin')->get() as $admin) {
            Notification::make()
                ->title('New award application')
                ->body("{$student->name} applied for the {$awardType->name}.")
                ->success()
                ->sendToDatabase($admin);
        }

        return $application;
    }

    /**
     * Why this student can't apply right now, or null when they can.
     */
    public function whyNot(User $student): ?string
    {
        if (! $student->isStudent()) {
            return 'Only student officers can apply for leadership awards.';
        }

        if ($this->activeApplication($student)) {
            return 'You already have an application in. You can have one pending or accepted application at a time.';
        }

        $evaluations = $student->participatingEvaluations();

        if (! $evaluations->exists()) {
            return 'You can apply once you have served in a council evaluation.';
        }

        if ($student->participatingEvaluations()->where('status', '!=', Evaluation::STATUS_COMPLETED)->exists()) {
            return 'You can apply once every evaluation you are part of is completed.';
        }

        return null;
    }

    public function activeApplication(User $student): ?LeadershipAwardApplication
    {
        return LeadershipAwardApplication::where('user_id', $student->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->with('awardType')
            ->latest()
            ->first();
    }
}
