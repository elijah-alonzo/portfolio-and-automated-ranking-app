<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', \App\Models\User::count()),
            Stat::make('Councils', \App\Models\Council::count()),
            Stat::make('Evaluations', \App\Models\Evaluation::count()),
        ];
    }
}
