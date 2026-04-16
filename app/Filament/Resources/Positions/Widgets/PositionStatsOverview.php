<?php

namespace App\Filament\Resources\Positions\Widgets;

use App\Models\Position;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PositionStatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $totalPositions = Position::count();
        $activePositions = Position::query()->where('is_active', true)->count();

        return [
            Stat::make('Total Positions', $totalPositions)
                ->icon('heroicon-o-rectangle-stack')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('All positions in the system')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Active Positions', $activePositions)
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Positions currently active')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
