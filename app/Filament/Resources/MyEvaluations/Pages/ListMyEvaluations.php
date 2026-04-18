<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Filament\Resources\MyEvaluations\Widgets\MyEvaluationStatsOverview;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMyEvaluations extends ListRecords
{
    protected static string $resource = MyEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MyEvaluationStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed')),
            'ongoing' => Tab::make('On Going')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ongoing')),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
        ];
    }
}
