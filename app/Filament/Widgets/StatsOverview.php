<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MyEvaluations\Pages\ListMyEvaluations;
use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $pendingEvaluationsAsAdviser = Evaluation::query()
            ->whereIn('status', ['closed', 'ongoing'])
            ->where('council_adviser_id', $user->id)
            ->pluck('id');

        $pendingAdviserAssignments = $pendingEvaluationsAsAdviser->isEmpty()
            ? 0
            : DB::table('evaluation_user')
                ->whereIn('evaluation_id', $pendingEvaluationsAsAdviser)
                ->count();

        $pendingParticipatingEvaluationIds = DB::table('evaluation_user')
            ->join('evaluations', 'evaluation_user.evaluation_id', '=', 'evaluations.id')
            ->where('evaluation_user.user_id', $user->id)
            ->whereIn('evaluations.status', ['closed', 'ongoing'])
            ->pluck('evaluation_user.evaluation_id');

        $pendingSelfAssignments = $pendingParticipatingEvaluationIds->count();

        $pendingPeerAssignments = EvaluationPeerEvaluator::query()
            ->where('evaluator_user_id', $user->id)
            ->whereHas('evaluation', fn ($query) => $query->whereIn('status', ['closed', 'ongoing']))
            ->count();

        $submittedPendingAdviserAssignments = EvaluationForm::query()
            ->where('evaluator_id', $user->id)
            ->where('evaluator_type', 'adviser')
            ->where('status', 'submitted')
            ->whereHas('evaluation', fn ($query) => $query->whereIn('status', ['closed', 'ongoing']))
            ->count();

        $submittedPendingSelfAssignments = EvaluationForm::query()
            ->where('evaluator_id', $user->id)
            ->where('evaluator_type', 'self')
            ->where('status', 'submitted')
            ->whereHas('evaluation', fn ($query) => $query->whereIn('status', ['closed', 'ongoing']))
            ->count();

        $submittedPendingPeerAssignments = EvaluationForm::query()
            ->where('evaluator_id', $user->id)
            ->where('evaluator_type', 'peer')
            ->where('status', 'submitted')
            ->whereHas('evaluation', fn ($query) => $query->whereIn('status', ['closed', 'ongoing']))
            ->count();

        $totalPendingAssignments = $pendingAdviserAssignments + $pendingSelfAssignments + $pendingPeerAssignments;
        $totalSubmittedPendingAssignments = $submittedPendingAdviserAssignments + $submittedPendingSelfAssignments + $submittedPendingPeerAssignments;
        $assignedNotSubmitted = max(0, $totalPendingAssignments - $totalSubmittedPendingAssignments);

        $councilIdsAsAdviser = Evaluation::query()
            ->where('council_adviser_id', $user->id)
            ->pluck('council_id');

        $councilIdsAsStudent = DB::table('evaluation_user')
            ->join('evaluations', 'evaluation_user.evaluation_id', '=', 'evaluations.id')
            ->where('evaluation_user.user_id', $user->id)
            ->pluck('evaluations.council_id');

        $totalCouncilsInvolved = $councilIdsAsAdviser
            ->merge($councilIdsAsStudent)
            ->filter()
            ->unique()
            ->count();

        $sharedStats = [
            Stat::make('Pending Assigned Evaluations', $assignedNotSubmitted)
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Assigned evaluation forms not yet submitted')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(ListMyEvaluations::getUrl()),
            Stat::make('Councils Involved', $totalCouncilsInvolved)
                ->icon('heroicon-o-building-library')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Councils where you are adviser or student')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(ListMyEvaluations::getUrl()),
        ];

        return $sharedStats;
    }
}
