<?php

namespace App\Filament\Resources\Positions;

use App\Filament\Resources\Positions\Pages\CreatePositions;
use App\Filament\Resources\Positions\Pages\EditPositions;
use App\Filament\Resources\Positions\Pages\ListPositions;
use App\Filament\Resources\Positions\Schemas\PositionsForm;
use App\Filament\Resources\Positions\Tables\PositionsTable;
use App\Models\Position;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PositionsResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static UnitEnum|string|null $navigationGroup = 'Council Management';

    protected static ?string $navigationLabel = 'Council Positions';

    protected static ?string $modelLabel = 'Position';

    protected static ?string $pluralModelLabel = 'Positions';

    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return PositionsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PositionsTable::configure($table);
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
            'index' => ListPositions::route('/'),
            'create' => CreatePositions::route('/create'),
            'edit' => EditPositions::route('/{record}/edit'),
        ];
    }
}
