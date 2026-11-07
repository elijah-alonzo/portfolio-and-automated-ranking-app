<?php

namespace App\Features\Evaluations\Controllers;

use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Queries\CouncilBoard;
use App\Features\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * "My Councils": the council evaluations a person advises or serves in.
 */
final class CouncilController
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $councils = Evaluation::query()
            ->where(fn ($query) => $query
                ->where('council_adviser_id', $user->id)
                ->orWhereHas('users', fn ($officers) => $officers->whereKey($user->id)))
            ->with(['council', 'adviser'])
            ->withCount('users')
            ->orderByDesc('academic_year')
            ->get()
            ->map(fn (Evaluation $evaluation) => [
                ...$this->summary($evaluation),
                'officers' => $evaluation->users_count,
                'role' => $evaluation->council_adviser_id === $user->id ? 'Adviser' : 'Officer',
            ]);

        return Inertia::render('app/Councils', ['councils' => $councils]);
    }

    public function show(Request $request, Evaluation $evaluation, CouncilBoard $board): Response
    {
        Gate::authorize('view', $evaluation);

        $evaluation->load(['council', 'adviser']);
        $canManage = Gate::allows('manage', $evaluation);

        return Inertia::render('app/Council', [
            'council' => [
                ...$this->summary($evaluation),
                'canManage' => $canManage,
                'canComplete' => $canManage && $evaluation->isOngoing() && $evaluation->areAllFormsSubmitted(),
            ],
            'officers' => $board->officers($evaluation, $request->user()),
            // The adviser picks officers from these while the evaluation is closed; the server re-checks every pick.
            'students' => $canManage && $evaluation->isClosed() ? $this->eligibleStudents($evaluation) : [],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(Evaluation $evaluation): array
    {
        return [
            'id' => $evaluation->id,
            'name' => $evaluation->council->name,
            'code' => $evaluation->council?->code,
            'academicYear' => (string) $evaluation->academic_year,
            'status' => $evaluation->status,
            'adviser' => $evaluation->adviser?->name,
            'adviserAvatar' => $evaluation->adviser?->avatarUrl(),
        ];
    }

    /**
     * @return list<array{id: int, name: string, department: ?string}>
     */
    private function eligibleStudents(Evaluation $evaluation): array
    {
        $departmentIds = $evaluation->council?->departments()->pluck('departments.id')->all() ?? [];

        return User::where('role', 'student')
            ->when($departmentIds, fn ($query) => $query->whereIn('department_id', $departmentIds))
            ->with('department:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (User $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'department' => $student->department?->name,
            ])
            ->all();
    }
}
