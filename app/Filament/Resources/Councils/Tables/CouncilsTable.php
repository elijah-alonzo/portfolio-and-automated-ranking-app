<?php

namespace App\Filament\Resources\Councils\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouncilsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => \App\Filament\Resources\Councils\CouncilResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('name')
                    ->label('Council Name')
                    ->weight('medium')
                    ->searchable(),

                TextColumn::make('code')
                    ->label('Council Code')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
            ])
            ->emptyStateHeading('No councils yet')
            ->emptyStateDescription('Councils will appear here once they are created.')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
