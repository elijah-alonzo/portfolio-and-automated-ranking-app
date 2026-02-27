<?php

namespace App\Filament\Resources\MyPortfolio\Pages;

use App\Filament\Resources\MyPortfolio\MyPortfolioResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewMyPortfolio extends Page
{
    protected static string $resource = MyPortfolioResource::class;

    protected static ?string $title = 'My Portfolio';
    
    protected string $view = 'Portfolio.PortfolioView';

    public ?User $record = null;

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
        // Always load the current user's record with relationships
        $this->record = auth()->user()->fresh()->load(['participatingEvaluations.council', 'participatingEvaluations.adviser', 'certificates']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_profile')
                ->label('Edit Profile')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->url(Filament::getProfileUrl()),
        ];
    }
}
