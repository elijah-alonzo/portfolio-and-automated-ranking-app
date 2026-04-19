<?php

namespace App\Filament\Resources\Evaluations\Tables;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => EvaluationResource::getUrl('view', ['record' => $record]))
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if ($user && $user->role === 'adviser') {
                    $query->where('council_adviser_id', $user->id);
                }

                return $query;
            })
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

                SelectFilter::make('adviser')
                    ->label('Adviser')
                    ->relationship('adviser', 'name')
                    ->placeholder('All Advisers')
                    ->searchable()
                    ->preload(),
            ]);
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
