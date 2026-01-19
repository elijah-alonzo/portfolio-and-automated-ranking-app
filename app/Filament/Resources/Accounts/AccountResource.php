<?php

namespace App\Filament\Resources\Accounts;

use App\Filament\Resources\Accounts\Pages\EditAccount;
use App\Filament\Resources\Accounts\Pages\IndexAccounts;
use App\Filament\Resources\Accounts\Pages\ViewAccount;
use App\Filament\Resources\Accounts\Schemas\AccountForm;
use App\Models\Account;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
        public static function infolist(Schema $schema): Schema
        {
            return $schema->components([
                // Main Header Section
                Grid::make([
                    'default' => 1,
                    'lg' => 3
                ])
                    ->schema([
                        // Left side - Name and contact info
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->extraAttributes([
                                        'style' => 'font-size: 48px; font-weight: 800;',
                                        'class' => 'text-gray-900 dark:text-white'
                                    ]),

                                // Email and Contact side by side
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('email')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->icon('heroicon-s-envelope')
                                            ->iconColor('gray')
                                            ->extraAttributes([
                                                'style' => 'font-size: 16px; line-height: 1.5;',
                                                'class' => 'text-gray-700 dark:text-gray-200 flex items-center'
                                            ]),

                                        TextEntry::make('contact_number')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->icon('heroicon-s-phone')
                                            ->iconColor('gray')
                                            ->extraAttributes([
                                                'style' => 'font-size: 16px; line-height: 1.5;',
                                                'class' => 'text-gray-700 dark:text-gray-200 flex items-center'
                                            ])
                                            ->placeholder('No contact number'),
                                    ]),

                                // Bio
                                TextEntry::make('bio')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->extraAttributes([
                                        'style' => 'font-size: 16px; line-height: 1.6; max-width: 500px;',
                                        'class' => 'text-gray-600 dark:text-gray-300'
                                    ])
                                    ->placeholder('No bio provided'),
                            ])
                            ->columnSpan([
                                'default' => 'full',
                                'lg' => 2
                            ]),

                        // Right side - Profile Picture
                        ImageEntry::make('pfp')
                            ->label('')
                            ->hiddenLabel()
                            ->height(250)
                            ->width(250)
                            ->circular()
                            ->alignCenter()
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'User'))
                            ->columnSpan([
                                'default' => 'full',
                                'lg' => 1
                            ]),
                    ])
                    ->extraAttributes([
                        'style' => 'padding: 48px 0; align-items: start;',
                    ])
                    ->columnSpanFull(),
            ]);
        }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    public static function form(Schema $schema): Schema
    {
        return AccountForm::configure($schema);
    }

    protected static UnitEnum|string|null $navigationGroup = 'Personal Management';

    protected static ?int $navigationSort = 2;

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => IndexAccounts::route('/'),
            'view' => ViewAccount::route('/{record}'),
            'edit' => EditAccount::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    protected static ?string $navigationLabel = 'Account';

    public static function canView($record): bool
    {
        return auth()->check() && auth()->id() === $record->id;
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->id() === $record->id;
    }

    public static function canCreate(): bool
    {
        return false; // Users can't create accounts
    }

    public static function canDelete($record): bool
    {
        return false; // Users can't delete their accounts
    }
}
