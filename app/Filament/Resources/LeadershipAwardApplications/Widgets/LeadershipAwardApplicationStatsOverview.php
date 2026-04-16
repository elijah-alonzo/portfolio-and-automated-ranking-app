<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Widgets;

use App\Models\LeadershipAwardApplication;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadershipAwardApplicationStatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $totalApplications = LeadershipAwardApplication::count();
        $pendingApplications = LeadershipAwardApplication::query()
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('Total Applications', $totalApplications)
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('All award applications')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Pending Applications', $pendingApplications)
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Applications awaiting review')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
