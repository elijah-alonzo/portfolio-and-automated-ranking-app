<?php

namespace App\Filament\Resources\Certificates\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use App\Filament\Resources\MyPortfolio\MyPortfolioResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (in_array(auth()->user()?->role, ['admin', 'adviser'], true)) {
            $actions[] = CreateAction::make();
        }

        if (auth()->user()?->role === 'student') {
            $actions[] = Action::make('back_portfolio')
                ->label('Back to Portfolio')
                ->color('gray')
                ->url(MyPortfolioResource::getUrl('index'));
        }

        return $actions;
    }
}
