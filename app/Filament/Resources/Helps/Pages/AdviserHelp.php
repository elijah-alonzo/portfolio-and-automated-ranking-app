<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class AdviserHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Adviser\'s Guide';

    protected string $view = 'Help.Adviser';

    public static function canAccess(array $parameters = []): bool
    {
        return static::canAccessRole(['adviser', 'admin']);
    }
}
