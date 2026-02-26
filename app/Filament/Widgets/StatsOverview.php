<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use App\Models\User;
use App\Filament\Resources\MyEvaluations\Pages\ListMyEvaluations;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        if ($user->role === 'admin') {
            return [
                Stat::make('Users', User::count())
                    ->icon('heroicon-o-user')
                    ->color('primary')
                    ->chart([1, 2, 3, 4, 5, 6, 7])
                    ->description('Total registered users')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->url(\App\Filament\Resources\Users\Pages\ListUsers::getUrl()),
                Stat::make('Councils', \App\Models\Council::count())
                    ->icon('heroicon-o-building-library')
                    ->color('primary')
                    ->chart([1, 2, 3, 4, 5, 6, 7])
                    ->description('Active councils')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->url(\App\Filament\Resources\Councils\Pages\ListCouncils::getUrl()),
                Stat::make('Evaluations', Evaluation::count())
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('primary')
                    ->chart([1, 2, 3, 4, 5, 6, 7])
                    ->description('Evaluations made')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->url(\App\Filament\Resources\Evaluations\Pages\ListEvaluations::getUrl()),
            ];
        }

        $submittedCriteria = EvaluationForm::query()
            ->where('evaluator_id', $user->id)
            ->where('status', 'submitted')
            ->get(['answers'])
            ->sum(fn (EvaluationForm $form) => is_array($form->answers) ? count($form->answers) : 0);

        $pendingEvaluationsAsAdviser = Evaluation::query()
            ->where('status', false)
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
            ->where('evaluations.status', false)
            ->pluck('evaluation_user.evaluation_id');

        $pendingSelfAssignments = $pendingParticipatingEvaluationIds->count();

        $pendingPeerAssignments = EvaluationPeerEvaluator::query()
            ->where('evaluator_user_id', $user->id)
            ->whereHas('evaluation', fn ($query) => $query->where('status', false))
            ->count();

        $submittedPendingAdviserAssignments = EvaluationForm::query()
            ->where('evaluator_id', $user->id)
            ->where('evaluator_type', 'adviser')
            ->where('status', 'submitted')
            ->whereHas('evaluation', fn ($query) => $query->where('status', false))
            ->count();

        $submittedPendingSelfAssignments = EvaluationForm::query()
            ->where('evaluator_id', $user->id)
            ->where('evaluator_type', 'self')
            ->where('status', 'submitted')
            ->whereHas('evaluation', fn ($query) => $query->where('status', false))
            ->count();

        $submittedPendingPeerAssignments = EvaluationForm::query()
            ->where('evaluator_id', $user->id)
            ->where('evaluator_type', 'peer')
            ->where('status', 'submitted')
            ->whereHas('evaluation', fn ($query) => $query->where('status', false))
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

        return [
            Stat::make('Submitted Criteria', $submittedCriteria)
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Total criteria you have submitted')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(ListMyEvaluations::getUrl()),
            Stat::make('Pending Assigned Evaluations', $assignedNotSubmitted)
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Assigned evaluation forms not yet submitted')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->url(ListMyEvaluations::getUrl()),
            Stat::make('Councils Involved', $totalCouncilsInvolved)
                ->icon('heroicon-o-building-library')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Councils where you are adviser or student')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(ListMyEvaluations::getUrl()),
        ];
    }
}
