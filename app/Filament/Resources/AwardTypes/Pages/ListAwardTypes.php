<?php

namespace App\Filament\Resources\AwardTypes\Pages;

use App\Filament\Resources\AwardTypes\AwardTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAwardTypes extends ListRecords
{
    protected static string $resource = AwardTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}