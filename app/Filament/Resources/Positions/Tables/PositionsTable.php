<?php

namespace App\Filament\Resources\Positions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => \App\Filament\Resources\Positions\PositionsResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('title')
                    ->label('Position Title')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('council.name')
                    ->label('Council')
                    ->searchable(),
                TextColumn::make('max_slots')
                    ->label('Max Slots')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('branch')
                    ->label('Branch')
                    ->placeholder('No branch'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('No positions yet')
            ->emptyStateDescription('Council positions will appear here once they are created.')
            ->filters([
                SelectFilter::make('council_id')
                    ->label('Council')
                    ->relationship('council', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
