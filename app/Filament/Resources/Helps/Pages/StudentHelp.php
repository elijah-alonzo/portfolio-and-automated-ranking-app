<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class StudentHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Student Help';

    protected string $view = 'Help.Student';

    public static function canAccess(array $parameters = []): bool
    {
        return static::canAccessRole(['student', 'admin']);
    }
}
