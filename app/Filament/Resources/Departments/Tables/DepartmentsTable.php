<?php

namespace App\Filament\Resources\Departments\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Department Name')
                    ->searchable(),

                TextColumn::make('councils_count')
                    ->label('Councils')
                    ->getStateUsing(fn ($record) => $record->councils()->count())
                    ->badge()
                    ->color('primary'),

                TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                //
            ]);
    }
}
