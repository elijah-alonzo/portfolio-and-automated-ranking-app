<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class AdminHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Admin Help';

    protected string $view = 'Help.Admin';

    public static function canAccess(array $parameters = []): bool
    {
        return static::canAccessRole(['admin']);
    }
}
