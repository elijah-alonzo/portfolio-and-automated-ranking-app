<?php

namespace App\Filament\Resources\Positions\Tables;

use App\Filament\Resources\Positions\PositionsResource;
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
            ->recordUrl(fn ($record) => PositionsResource::getUrl('edit', ['record' => $record]))
            ->heading('Positions')
            ->description('List of all council positions available for students.')
            ->columns([
                TextColumn::make('title')
                    ->label('Position Title')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('councils_count')
                    ->label('Councils')
                    ->getStateUsing(function ($record) {
                        return $record->councilAssignments()->count();
                    })
                    ->badge()
                    ->color('primary')
                    ->tooltip(function ($record) {
                        $names = $record->councilAssignments()
                            ->with('council:id,name')
                            ->get()
                            ->pluck('council.name')
                            ->filter()
                            ->values();

                        if ($names->isEmpty()) {
                            return 'No councils assigned';
                        }

                        return $names->implode(', ');
                    }),
                TextColumn::make('max_slots')
                    ->label('Max Slots')
                    ->getStateUsing(function ($record) {
                        $min = $record->councilAssignments()->min('max_slots');
                        $max = $record->councilAssignments()->max('max_slots');

                        if ($min === null || $max === null) {
                            return '-';
                        }

                        return $min === $max ? (string) $min : $min.'-'.$max;
                    })
                    ->badge()
                    ->color('warning'),
                TextColumn::make('hierarchy')
                    ->label('Hierarchy')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
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
                    ->label('Registered')
                    ->dateTime(),
            ])
            ->emptyStateHeading('No positions yet')
            ->emptyStateDescription('Council positions will appear here once they are created.')
            ->filters([
                SelectFilter::make('council_id')
                    ->label('Council')
                    ->relationship('councils', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ]);
    }
}
