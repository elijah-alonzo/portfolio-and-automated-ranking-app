<?php

namespace App\Filament\Resources\AwardTypes\Pages;

use App\Filament\Resources\AwardTypes\AwardTypeResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListAwardTypes extends ListRecords
{
    protected static string $resource = AwardTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}