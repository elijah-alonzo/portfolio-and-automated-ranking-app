<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class UserHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Users';

    protected string $view = 'Help.Users';
}
