<?php

namespace App\Filament\Resources\Helps;

use App\Filament\Resources\Helps\Pages\CreateHelp;
use App\Filament\Resources\Helps\Pages\EditHelp;
use App\Filament\Resources\Helps\Pages\ListHelps;
use App\Filament\Resources\Helps\Schemas\HelpForm;
use App\Filament\Resources\Helps\Tables\HelpsTable;
use App\Models\Help;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HelpResource extends Resource
{
    protected static ?string $model = Help::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrench;

    public static function form(Schema $schema): Schema
    {
        return HelpForm::configure($schema);
    }

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Help';
    }

    public static function table(Table $table): Table
    {
        return HelpsTable::configure($table);
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
            'index' => ListHelps::route('/'),
            'create' => CreateHelp::route('/create'),
            'edit' => EditHelp::route('/{record}/edit'),
        ];
    }
}
