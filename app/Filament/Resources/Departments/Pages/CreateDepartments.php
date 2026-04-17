<?php

namespace App\Filament\Resources\Departments\Pages;

use App\Filament\Resources\Departments\DepartmentsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartments extends CreateRecord
{
    protected static string $resource = DepartmentsResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
