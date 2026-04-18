<?php

namespace App\Filament\Resources\Evaluations\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EvaluationRecommendationsOverview extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Executive Positions', '3.00-2.41')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Recommended evaluation score for Executive Positions')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Legislative Positions', '2.40-1.81')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Recommended evaluation score for Legislative Positions')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Judicial and Mayoral Positions', '1.80-1.21')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Recommended evaluation score for Judicial and Mayoral Positions')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
