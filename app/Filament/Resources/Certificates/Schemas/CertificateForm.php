<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Certificate Information')
                    ->description('Upload and manage certificate details')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_ids')
                            ->label('Students')
                            ->options(User::where('role', 'student')->pluck('name', 'id'))
                            ->searchable()
                            ->multiple()
                            ->required(fn ($record) => auth()->user()?->role === 'admin' && $record === null)
                            ->visible(fn ($record) => auth()->user()?->role === 'admin' && $record === null)
                            ->columnSpan(2),
                        Select::make('user_id')
                            ->label('Student')
                            ->relationship('user', 'name', fn (Builder $query) => $query->where('role', 'student'))
                            ->searchable()
                            ->required(fn ($record) => auth()->user()?->role === 'admin' && $record !== null)
                            ->visible(fn ($record) => auth()->user()?->role === 'admin' && $record !== null)
                            ->columnSpan(2),
                        FileUpload::make('file_path')
                            ->columnSpan(2)
                            ->label('Certificate File (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public')
                            ->directory('certificates')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->required(),

                        TextInput::make('certification_name')
                            ->columnSpan(1)
                            ->label('Certification Name')
                            ->prefixIcon('heroicon-o-document-text')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter certification name'),

                        DatePicker::make('date_issued')
                            ->columnSpan(1)                       
                            ->label('Date Issued')
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2)
                    ->extraAttributes(['class' => 'mb-6']),
            ]);
    }
}
