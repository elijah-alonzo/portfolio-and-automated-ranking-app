<?php

namespace App\Features\Users\Controllers;

use App\Features\Awards\Actions\ApplyForLeadershipAward;
use App\Features\Awards\Models\AwardType;
use App\Features\Certificates\Models\Certificate;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * A student's leadership portfolio: who they are, the councils they served, their ranks and certificates.
 */
final class PortfolioController
{
    public function mine(Request $request, ApplyForLeadershipAward $awards): Response
    {
        $student = $request->user();
        $application = $awards->activeApplication($student);

        return $this->render($student, award: [
            'application' => $application ? [
                'status' => $application->status,
                'awardType' => $application->awardType?->name,
            ] : null,
            'blockedReason' => $awards->whyNot($student),
            'awardTypes' => AwardType::orderBy('name')->get(['id', 'name', 'description']),
        ]);
    }

    public function student(User $student): Response
    {
        Gate::authorize('manageStudent', $student);

        return $this->render($student, award: null);
    }

    /**
     * @param  array<string, mixed>|null  $award
     */
    private function render(User $student, ?array $award): Response
    {
        $student->load(['department:id,name', 'participatingEvaluations.council', 'certificates', 'evaluationRanks']);
        $ranks = $student->evaluationRanks->keyBy('evaluation_id');
        $positions = $student->participatingEvaluations()->pluck('evaluation_user.position', 'evaluations.id');

        return Inertia::render('app/Portfolio', [
            'person' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'contactNumber' => $student->contact_number,
                'bio' => $student->bio,
                'avatar' => $student->avatarUrl(),
                'department' => $student->department?->name,
            ],
            'experience' => $student->participatingEvaluations
                ->sortByDesc('academic_year')
                ->values()
                ->map(fn (Evaluation $evaluation) => [
                    'id' => $evaluation->id,
                    'council' => $evaluation->council->name,
                    'academicYear' => (string) $evaluation->academic_year,
                    'position' => $positions->get($evaluation->id),
                    'status' => $evaluation->status,
                    'finalScore' => $ranks->get($evaluation->id)?->final_score !== null ? (float) $ranks->get($evaluation->id)->final_score : null,
                    'rank' => $ranks->get($evaluation->id)?->rank?->label(),
                ]),
            'certificates' => $student->certificates
                ->sortByDesc('date_issued')
                ->values()
                ->map(fn (Certificate $certificate) => [
                    'id' => $certificate->id,
                    'name' => $certificate->certification_name,
                    'dateIssued' => $certificate->date_issued->toDateString(),
                    'url' => $certificate->fileUrl(),
                ]),
            'award' => $award,
        ]);
    }
}
