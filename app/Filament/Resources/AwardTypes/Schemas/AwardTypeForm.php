<?php

namespace App\Filament\Resources\AwardTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AwardTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->description('Provide the basic information for the award type.')
                    ->columnspanFull()
                    ->schema([
                        TextInput::make('name')
                            ->prefixIcon('heroicon-o-trophy')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
