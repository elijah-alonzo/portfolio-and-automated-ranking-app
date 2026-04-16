<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserRoleStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $adviserUsers = User::query()->where('role', 'adviser')->count();
        $studentUsers = User::query()->where('role', 'student')->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Advisers', $adviserUsers)
                ->icon('heroicon-o-academic-cap')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Users with adviser role')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Students', $studentUsers)
                ->icon('heroicon-o-user')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->description('Users with student role')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
