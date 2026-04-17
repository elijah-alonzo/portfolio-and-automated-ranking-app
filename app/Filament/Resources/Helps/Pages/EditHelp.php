<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHelp extends EditRecord
{
    protected static string $resource = HelpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
