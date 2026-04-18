<?php

namespace App\Filament\Resources\Helps\Pages;

use App\Filament\Resources\Helps\HelpResource;

class DepartmentHelp extends HelpPage
{
    protected static string $resource = HelpResource::class;

    protected static ?string $title = 'Departments';

    protected string $view = 'Help.Departments';
}
