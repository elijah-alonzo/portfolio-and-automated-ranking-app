<?php

namespace App\Filament\Resources\Helps\Pages;

use Filament\Resources\Pages\Page;

abstract class HelpPage extends Page
{
    public function getSubNavigation(): array
    {
        return static::getResource()::getSubNavigation($this);
    }

    protected static function canAccessRole(array $roles): bool
    {
        $role = auth()->user()?->role;

        return $role !== null && in_array($role, $roles, true);
    }
}
