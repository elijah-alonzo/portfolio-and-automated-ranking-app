<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Pages;

use App\Filament\Resources\LeadershipAwardApplications\LeadershipAwardApplicationResource;
use App\Filament\Resources\LeadershipAwardApplications\Widgets\LeadershipAwardApplicationStatsOverview;
use App\Models\LeadershipAwardApplication;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeadershipAwardApplications extends ListRecords
{
    protected static string $resource = LeadershipAwardApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LeadershipAwardApplicationStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => LeadershipAwardApplication::count()),
            'pending' => Tab::make('Pending')
                ->badge(fn () => LeadershipAwardApplication::where('status', 'pending')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'accepted' => Tab::make('Accepted')
                ->badge(fn () => LeadershipAwardApplication::where('status', 'accepted')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'accepted')),
            'rejected' => Tab::make('Rejected')
                ->badge(fn () => LeadershipAwardApplication::where('status', 'rejected')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
        ];
    }
}