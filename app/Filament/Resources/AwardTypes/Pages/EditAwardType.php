<?php

namespace App\Filament\Resources\AwardTypes\Pages;

use App\Filament\Resources\AwardTypes\AwardTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAwardType extends EditRecord
{
    protected static string $resource = AwardTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
