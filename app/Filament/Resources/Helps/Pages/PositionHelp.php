<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class PositionHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Positions';

    protected string $view = 'Help.Positions';
}
