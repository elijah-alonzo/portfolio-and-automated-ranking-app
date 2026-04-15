<?php

namespace App\Filament\Resources\Positions\Pages;

use App\Filament\Resources\Positions\PositionsResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePositions extends CreateRecord
{
    protected static string $resource = PositionsResource::class;

    protected function getRedirectUrl(): string
    {
        return PositionsResource::getUrl('index');
    }
}
