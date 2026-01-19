<?php
namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;
    protected static bool $shouldRegisterNavigation = false;

    public function mount(int|string $record = null): void
    {
        $this->record = auth()->user();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit Account')
                ->url(fn (): string => AccountResource::getUrl('edit', ['record' => $this->record]))
        ];
    }

    // The infolist method has been moved to AccountResource.
}
