<?php

namespace App\Filament\Resources\Certificates\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('back_portfolio')
                ->label('Back to Portfolio')
                ->color('gray')
                ->url(\App\Filament\Resources\MyPortfolio\MyPortfolioResource::getUrl('index')),
        ];
    }
}
