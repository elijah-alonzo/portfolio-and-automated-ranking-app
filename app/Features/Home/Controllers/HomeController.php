<?php

namespace App\Features\Home\Controllers;

use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationRank;
use App\Features\Evaluations\Queries\PendingForms;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The first screen after signing in: the forms waiting on you, then your councils.
 */
final class HomeController
{
    public function __invoke(Request $request, PendingForms $pending): Response
    {
        $user = $request->user();

        $councils = Evaluation::query()
            ->where(fn ($query) => $query
                ->where('council_adviser_id', $user->id)
                ->orWhereHas('users', fn ($officers) => $officers->whereKey($user->id)))
            ->with('council')
            ->orderByRaw("case status when 'ongoing' then 0 when 'closed' then 1 else 2 end")
            ->orderByDesc('academic_year')
            ->limit(6)
            ->get()
            ->map(fn (Evaluation $evaluation) => [
                'id' => $evaluation->id,
                'name' => $evaluation->council->name,
                'academicYear' => (string) $evaluation->academic_year,
                'status' => $evaluation->status,
                'role' => $evaluation->council_adviser_id === $user->id ? 'Adviser' : 'Officer',
            ]);

        $latestRank = EvaluationRank::where('user_id', $user->id)
            ->whereNotNull('final_score')
            ->with('evaluation.council')
            ->latest('updated_at')
            ->first();

        return Inertia::render('app/Home', [
            'tasks' => $pending->for($user),
            'councils' => $councils,
            'latestRank' => $latestRank ? [
                'council' => $latestRank->evaluation?->council?->name,
                'academicYear' => (string) $latestRank->evaluation?->academic_year,
                'finalScore' => (float) $latestRank->final_score,
                'rank' => $latestRank->rank?->label(),
            ] : null,
        ]);
    }
}
