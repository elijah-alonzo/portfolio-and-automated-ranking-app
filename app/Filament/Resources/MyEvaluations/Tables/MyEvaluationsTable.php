<?php

namespace App\Filament\Resources\MyEvaluations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MyEvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => \App\Filament\Resources\MyEvaluations\MyEvaluationResource::getUrl('view', ['record' => $record]))
            ->columns([
                ImageColumn::make('council.logo')
                    ->label(' ')
                    ->circular()
                    ->size(40)
                    ->grow(false)
                    ->alignCenter()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->council->name ?? 'Council') . '&color=7F9CF5&background=EBF4FF')
                    ->extraAttributes(['class' => 'ring-1 ring-gray-100 dark:ring-gray-800']),

                TextColumn::make('council.name')
                    ->label('Council')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('adviser.name')
                    ->label('Adviser')
                    ->searchable()
                    ->sortable(),

                ImageColumn::make('students_images')
                    ->label('Students')
                    ->stacked()
                    ->limit(4)
                    ->limitedRemainingText()
                    ->circular()
                    ->getStateUsing(function ($record) {
                        return $record->users->map(function ($user) {
                            return $user->pfp ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';
                        })->toArray();
                    })
                    ->tooltip(function ($record) {
                        $userNames = $record->users->pluck('name')->toArray();
                        if (empty($userNames)) {
                            return 'No students assigned';
                        }
                        return 'Students: ' . implode(', ', $userNames);
                    }),

                TextColumn::make('academic_year')
                    ->label('Academic Year')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('status')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('warning')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-clock')
                    ->disabled(fn ($record) => auth()->id() !== $record->council_adviser_id)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        true => 'Completed',
                        false => 'Pending',
                    ])
                    ->placeholder('All Statuses'),

                SelectFilter::make('academic_year')
                    ->label('Academic Year')
                    ->options(function () {
                        return \App\Models\Evaluation::distinct()
                            ->pluck('academic_year', 'academic_year')
                            ->sort()
                            ->toArray();
                    })
                    ->placeholder('All Years')
                    ->searchable(),

                SelectFilter::make('council')
                    ->label('Council')
                    ->relationship('council', 'name')
                    ->placeholder('All Councils')
                    ->searchable()
                    ->preload(),
            ])
            ->toolbarActions([]);
    }
}
