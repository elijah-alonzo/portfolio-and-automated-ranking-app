<?php

namespace App\Filament\Resources\Certificates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => \App\Filament\Resources\Certificates\CertificateResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('certification_name')
                    ->label('Certification Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Uploader')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date_issued')
                    ->label('Date Issued')
                    ->date()
                    ->sortable(),

                TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(fn () => 'View PDF')
                    ->url(fn ($record) => Storage::url($record->file_path), true)
                    ->color('info')
                    ->openUrlInNewTab(),

                TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
