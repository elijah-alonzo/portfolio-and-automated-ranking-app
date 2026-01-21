<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', \App\Models\User::count())
                ->icon('heroicon-o-user')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(\App\Filament\Resources\Users\Pages\ListUsers::getUrl()),
            Stat::make('Councils', \App\Models\Council::count())
                ->icon('heroicon-o-building-library')
                ->color('info')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Active councils')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(\App\Filament\Resources\Councils\Pages\ListCouncils::getUrl()),
            Stat::make('Evaluations', \App\Models\Evaluation::count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Evaluations made')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->url(\App\Filament\Resources\Evaluations\Pages\ListEvaluations::getUrl()),
        ];
    }
}
