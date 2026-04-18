<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Councils\Pages\ListCouncils;
use App\Filament\Resources\Evaluations\Pages\ListEvaluations;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Council;
use App\Models\Evaluation;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->role === 'admin';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->icon('heroicon-o-user')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('The total number of users registered in the system')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(ListUsers::getUrl()),

            Stat::make('Councils', Council::count())
                ->icon('heroicon-o-building-library')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Active councils who can be involved in evaluations')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(ListCouncils::getUrl()),

            Stat::make('Evaluations', Evaluation::count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('The number of evaluation instances created')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(ListEvaluations::getUrl()),
        ];
    }
}
