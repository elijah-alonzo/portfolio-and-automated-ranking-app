<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                        FileUpload::make('file_path')
                            ->columnSpan(2)
                            ->label('Certificate File (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('certificates')
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
