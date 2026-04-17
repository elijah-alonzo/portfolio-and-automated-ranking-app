<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;


class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => \App\Filament\Resources\Users\UserResource::getUrl('edit', ['record' => $record]))
            ->columns([
                ColumnGroup::make('User Information', [
                    ImageColumn::make('pfp')
                        ->label('Picture')
                        ->circular()
                        ->size(40)
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                        ,
                    TextColumn::make('name')
                        ->label('Name')
                        ->weight('medium')
                        ->searchable()
                        ->description(fn ($record) => $record->department?->name ?? 'No department'),
                ]),
                ColumnGroup::make('Contact Information', [
                    TextColumn::make('email')
                        ->label('Email')
                        ->searchable()
                        ->copyable()
                        ->icon('heroicon-o-envelope'),
                    TextColumn::make('contact_number')
                        ->label('Contact')
                        ->icon('heroicon-o-phone')
                        ->placeholder('No contact')
                        ->copyable(),
                ]),

                IconColumn::make('role')
                    ->label('Role')
                    ->icon(fn (string $state): string => match ($state) {
                        'admin' => 'heroicon-o-shield-check',
                        'adviser' => 'heroicon-o-academic-cap',
                        'student' => 'heroicon-o-user',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'gray',
                        'adviser' => 'gray',
                        'student' => 'gray',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => ucfirst($state)),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Registered'),
            ])
            ->emptyStateHeading('No users yet')
            ->emptyStateDescription('Users will appear here once they are registered.')
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'adviser' => 'Adviser',
                        'student' => 'Student',
                    ]),
                SelectFilter::make('department')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ;
    }
}
