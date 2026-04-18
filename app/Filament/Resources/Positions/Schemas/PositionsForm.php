<?php

namespace App\Filament\Resources\Positions\Schemas;

use App\Models\Council;
use App\Models\CouncilPosition;
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
                        TextInput::make('title')
                            ->label('Position Title')
                            ->prefixIcon('heroicon-o-identification')
                            ->default(fn ($record) => $record?->title)
                            ->required()
                            ->maxLength(255)
                            ->rule(fn ($record) => 'unique:positions,title,'.($record?->id ?? 'NULL').',id')
                            ->columnSpan(1),
                        Select::make('council_ids')
                            ->label('Councils')
                            ->options(fn () => Council::orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->required()
                            ->default(fn ($record) => $record
                                ? CouncilPosition::where('position_id', $record->id)
                                    ->pluck('council_id')
                                    ->toArray()
                                : null)
                            ->columnSpan(1),
                        Select::make('branch')
                            ->label('Branch')
                            ->options([
                                'Executive' => 'Executive',
                                'Legislative' => 'Legislative',
                                'Judiciary' => 'Judiciary',
                                'Mayoral' => 'Mayoral',
                            ])
                            ->default(fn ($record) => $record?->branch)
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('hierarchy')
                            ->label('Hierarchy')
                            ->prefixIcon('heroicon-o-chart-bar')
                            ->numeric()
                            ->minValue(1)
                            ->default(fn ($record) => $record?->hierarchy)
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('max_slots')
                            ->label('Max Slots')
                            ->prefixIcon('heroicon-o-adjustments-horizontal')
                            ->numeric()
                            ->minValue(1)
                            ->default(fn ($record) => $record
                                ? CouncilPosition::where('position_id', $record->id)->value('max_slots')
                                : null)
                            ->required()
                            ->columnSpan(1),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->inline(false)
                            ->helperText('Activate or deactivate this position')
                            ->default(fn ($record) => $record?->is_active ?? true)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }
}
