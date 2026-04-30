<?php

namespace App\Filament\Resources\AwardTypes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AwardTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Award Types')
            ->description('List of all award types associated with councils.')
            ->columns([
                TextColumn::make('name')
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
                    ->dateTime()
                    ->label('Created'),
            ])
            ->filters([]);
    }
}
