<?php

namespace App\Filament\Resources\AwardTypes;

use App\Filament\Resources\AwardTypes\Pages\CreateAwardType;
use App\Filament\Resources\AwardTypes\Pages\EditAwardType;
use App\Filament\Resources\AwardTypes\Pages\ListAwardTypes;
use App\Filament\Resources\AwardTypes\Schemas\AwardTypeForm;
use App\Filament\Resources\AwardTypes\Tables\AwardTypesTable;
use App\Models\AwardType;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class AwardTypeResource extends Resource
{
    protected static ?string $model = AwardType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $navigationLabel = 'Award Types';
    
    protected static UnitEnum|string|null $navigationGroup = 'Award Management';

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return AwardTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AwardTypesTable::configure($table);
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
            'index' => ListAwardTypes::route('/'),
            'create' => CreateAwardType::route('/create'),
            'edit' => EditAwardType::route('/{record}/edit'),
        ];
    }
}