<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class OverviewHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Introduction';

    protected string $view = 'Help.Overview';
}
