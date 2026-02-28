<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role, ['admin']);
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'admin';
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'admin';
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'admin';
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'admin';
    }

    public static function canDeleteAny(): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    protected static UnitEnum|string|null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
