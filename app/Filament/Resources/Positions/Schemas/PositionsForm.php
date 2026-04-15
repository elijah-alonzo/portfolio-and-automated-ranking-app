<?php

namespace App\Filament\Resources\Positions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PositionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Council Position')
                    ->description('Define the positions available for a council term')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('council_id')
                            ->label('Council')
                            ->relationship('council', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('title')
                            ->label('Position Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('max_slots')
                            ->label('Max Slots')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('branch')
                            ->label('Branch')
                            ->maxLength(255)
                            ->placeholder('Legislative, Executive, Judicial, Coordinators')
                            ->columnSpan(1),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->inline(false)
                            ->helperText('Activate or deactivate this position')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }
}
