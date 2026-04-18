<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('currentPassword')
            ->label('Current password')
            ->password()
            ->autocomplete('current-password')
            ->currentPassword(guard: Filament::getAuthGuard())
            ->revealable(filament()->arePasswordsRevealable())
            ->requiredWith('password')
            ->dehydrated(false);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return parent::defaultForm($schema)->inlineLabel(false);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Profile Information')
                    ->description("Update your account's profile information and email address.")
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('pfp')
                            ->label('Profile Picture')
                            ->image()
                            ->directory('profile-pictures')
                            ->imageEditor()
                            ->maxSize(2048),

                        TextInput::make('name')
                            ->label('Name')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('contact_number')
                            ->label('Contact Info')
                            ->prefixIcon('heroicon-o-phone')
                            ->tel()
                            ->maxLength(255),

                        Textarea::make('bio')
                            ->label('Description')
                            ->rows(4),
                    ]),

                Section::make('Update Password')
                    ->description('Ensure your account is using a long, random password to stay secure.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        $this->getCurrentPasswordFormComponent()
                            ->columnSpan(1),
                        $this->getPasswordFormComponent()
                            ->columnSpan(1),
                        $this->getPasswordConfirmationFormComponent()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
