<?php

namespace App\Filament\Resources\MyEvaluations\Widgets;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyEvaluationStatsOverview extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $baseQuery = MyEvaluationResource::getEloquentQuery();

        $totalEvaluations = (clone $baseQuery)->count();
        $openEvaluations = (clone $baseQuery)
            ->whereIn('status', ['closed', 'ongoing'])
            ->count();

        return [
            Stat::make('My Evaluations', $totalEvaluations)
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Evaluations assigned to you')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('My Open Evaluations', $openEvaluations)
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Your evaluations not yet completed')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
