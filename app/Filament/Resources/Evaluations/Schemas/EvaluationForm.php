<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Evaluation Information')
                    ->description('Manage evaluation details such as council, academic year, and evaluation period')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('council_id')
                            ->label('Council')
                            ->relationship('council', 'name', fn ($query) => $query->where('is_active', true))
                            ->required()
                            ->prefixIcon('heroicon-m-building-office')
                            ->placeholder('Select a council')
                            ->live(),
                        Select::make('council_adviser_id')
                            ->label('Council Adviser')
                            ->relationship('adviser', 'name', fn ($query) => $query->whereIn('role', ['admin', 'adviser']))
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->required()
                            ->prefixIcon('heroicon-m-user-circle'),
                        Select::make('academic_year')
                            ->label('Academic Year')
                            ->required()
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->options(self::getAcademicYearOptions())
                            ->placeholder('Select academic year')
                            ->searchable()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('council_id', $get('council_id'))
                            ),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }

    protected static function getAcademicYearOptions(): array
    {
        $options = [];
        for ($year = 2022; $year <= 2032; $year++) {
            $label = $year . '-' . ($year + 1);
            $options[$label] = $label;
        }

        return $options;
    }
}