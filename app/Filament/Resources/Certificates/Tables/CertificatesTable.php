<?php

namespace App\Filament\Resources\Certificates\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => auth()->user()?->role === 'admin'
                ? \App\Filament\Resources\Certificates\CertificateResource::getUrl('edit', ['record' => $record])
                : null)
            ->columns([
                TextColumn::make('certification_name')
                    ->label('Certification Name')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable(),

                TextColumn::make('date_issued')
                    ->label('Date Issued')
                    ->date(),

                TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime(),
            ])
            ->actions([
                Action::make('download')
                    ->label(' ')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn ($record) => url(Storage::disk('public')->url($record->file_path)), true)
                    ->openUrlInNewTab(),
            ])
            ->filters([]);
    }
}
