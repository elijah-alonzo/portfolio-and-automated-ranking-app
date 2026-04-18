<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class ApplicationHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Applications';

    protected string $view = 'Help.Applications';
}
