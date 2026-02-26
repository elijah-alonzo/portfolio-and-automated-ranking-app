<?php

namespace App\Filament\Resources\MyPortfolio\Pages;

use App\Filament\Resources\MyPortfolio\MyPortfolioResource;
use App\Models\User;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMyPortfolio extends ViewRecord
{
    protected static string $resource = MyPortfolioResource::class;

    protected static ?string $title = 'My Portfolio';

    public function getHeading(): string|Htmlable
    {
        return 'Welcome, ' . auth()->user()->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Your leadership portfolio and evaluation history';
    }

    public function mount(int|string $record = null): void
    {
        // Always load the current user's record
        $this->record = auth()->user();
        $this->authorizeAccess();
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        // Left Column - Profile Card
                        Section::make('Profile')
                            ->description('Your profile details')
                            ->icon('heroicon-o-user')
                            ->schema([
                                ImageEntry::make('pfp')
                                    ->hiddenLabel()
                                    ->circular()
                                    ->size(120)
                                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=036635&background=E8F5E9')
                                    ->columnSpanFull()
                                    ->alignCenter(),
                                
                                TextEntry::make('name')
                                    ->hiddenLabel()
                                    ->color('primary')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->columnSpanFull()
                                    ->alignCenter(),
                                
                                TextEntry::make('bio')
                                    ->hiddenLabel()
                                    ->default('No biography provided')
                                    ->columnSpanFull(),
                                
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable(),
                                        
                                        TextEntry::make('contact_number')
                                            ->label('Phone')
                                            ->icon('heroicon-o-phone')
                                            ->default('Not provided'),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),

                        // Right Column - Leadership Experience
                        Section::make('Leadership Experience')
                            ->description('Your evaluations and positions in student organizations')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                RepeatableEntry::make('participatingEvaluations')
                                    ->label('')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('council.name')
                                                    ->label('Organization')
                                                    ->icon('heroicon-o-building-office-2')
                                                    ->weight('bold')
                                                    ->color('primary'),
                                                TextEntry::make('academic_year')
                                                    ->label('Academic Year')
                                                    ->icon('heroicon-o-calendar')
                                                    ->badge()
                                                    ->color('success'),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('pivot.position')
                                                    ->label('Position')
                                                    ->icon('heroicon-o-briefcase')
                                                    ->badge()
                                                    ->color('warning')
                                                    ->default('Member'),
                                                TextEntry::make('status')
                                                    ->label('Status')
                                                    ->icon('heroicon-o-clipboard-document-check')
                                                    ->badge()
                                                    ->color(fn ($state) => $state ? 'success' : 'warning')
                                                    ->formatStateUsing(fn ($state) => $state ? 'Completed' : 'Pending'),
                                            ]),
                                        TextEntry::make('adviser.name')
                                            ->label('Council Adviser')
                                            ->icon('heroicon-o-user-group')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull()
                                    ->contained(false),
                            ])
                            ->columnSpan(2),
                    ])->columnSpanFull(),

                Section::make('Certificates & Achievements')
                    ->description('Your uploaded certificates and recognitions')
                    ->icon('heroicon-o-trophy')
                    ->schema([
                        RepeatableEntry::make('certificates')
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('certification_name')
                                            ->label('Certificate Name')
                                            ->icon('heroicon-o-document-text')
                                            ->weight('bold')
                                            ->color('primary'),
                                        TextEntry::make('date_issued')
                                            ->label('Date Issued')
                                            ->icon('heroicon-o-calendar-days')
                                            ->date('F j, Y')
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('file_path')
                                            ->label('File')
                                            ->icon('heroicon-o-paper-clip')
                                            ->url(fn ($state) => asset('storage/' . $state))
                                            ->openUrlInNewTab()
                                            ->color('primary')
                                            ->formatStateUsing(fn () => 'View Certificate'),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->contained(false),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
