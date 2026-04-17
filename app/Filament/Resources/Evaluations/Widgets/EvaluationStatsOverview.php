<?php

namespace App\Filament\Resources\Evaluations\Widgets;

use App\Models\Evaluation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EvaluationStatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $totalEvaluations = Evaluation::count();
        $openEvaluations = Evaluation::query()
            ->whereIn('status', ['closed', 'ongoing'])
            ->count();

        return [
            Stat::make('Total Evaluations', $totalEvaluations)
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('All evaluations in the system')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Open Evaluations', $openEvaluations)
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Not completed evaluations')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
