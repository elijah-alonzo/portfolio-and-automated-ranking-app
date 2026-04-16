<?php

namespace App\Filament\Resources\Councils\Widgets;

use App\Models\Council;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CouncilStatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $totalCouncils = Council::count();
        $activeCouncils = Council::query()->where('is_active', true)->count();

        return [
            Stat::make('Total Councils', $totalCouncils)
                ->icon('heroicon-o-building-library')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('All councils in the system')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Active Councils', $activeCouncils)
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Councils currently active')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
