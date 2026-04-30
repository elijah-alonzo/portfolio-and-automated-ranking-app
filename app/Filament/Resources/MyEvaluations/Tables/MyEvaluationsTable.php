<?php

namespace App\Filament\Resources\MyEvaluations\Tables;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Models\Evaluation;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MyEvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => MyEvaluationResource::getUrl('view', ['record' => $record]))
            ->heading('My Councils')
            ->description('List of the councils you are involved in.')
            ->columns([
                TextColumn::make('council.name')
                    ->label('Council')
                    ->searchable(),

                TextColumn::make('adviser.name')
                    ->label('Adviser')
                    ->searchable(),

                ImageColumn::make('students_images')
                    ->label('Students')
                    ->stacked()
                    ->limit(4)
                    ->limitedRemainingText()
                    ->circular()
                    ->getStateUsing(function ($record) {
                        return $record->users->map(function ($user) {
                            return self::resolveProfileImageUrl($user->pfp, $user->name);
                        })->toArray();
                    })
                    ->tooltip(function ($record) {
                        $userNames = $record->users->pluck('name')->toArray();
                        if (empty($userNames)) {
                            return 'No students assigned';
                        }

                        return 'Students: '.implode(', ', $userNames);
                    }),

                TextColumn::make('academic_year')
                    ->label('Academic Year')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'closed' => 'Closed',
                        'ongoing' => 'On going',
                        'completed' => 'Completed',
                        default => 'Unknown',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'completed' => 'success',
                        'ongoing' => 'info',
                        'closed' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'closed' => 'Closed',
                        'ongoing' => 'On going',
                        'completed' => 'Completed',
                    ])
                    ->placeholder('All Statuses'),

                SelectFilter::make('academic_year')
                    ->label('Academic Year')
                    ->options(function () {
                        return Evaluation::distinct()
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

    protected static function resolveProfileImageUrl(?string $pfp, string $name): string
    {
        $fallbackUrl = 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';

        if (blank($pfp)) {
            return $fallbackUrl;
        }

        $path = str_replace('\\', '/', $pfp);

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $relativePath = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : ltrim($path, '/');

        return asset('storage/'.$relativePath);
    }
}
