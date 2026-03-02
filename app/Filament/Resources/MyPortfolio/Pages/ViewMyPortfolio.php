<?php

namespace App\Filament\Resources\MyPortfolio\Pages;

use App\Filament\Resources\MyPortfolio\MyPortfolioResource;
use App\Models\User;
use App\Models\AwardType;
use App\Models\LeadershipAwardApplication;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms;
use Filament\Notifications\Notification;

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
        $this->record = auth()->user()->fresh()->load([
            'participatingEvaluations.council.awardType', 
            'participatingEvaluations.adviser', 
            'certificates',
            'evaluationRanks'
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('apply_for_leadership_award')
                ->label('Apply for Leadership Award')
                ->color('success')
                ->icon('heroicon-m-trophy')
                ->visible(fn() => auth()->user()->role === 'student')
                ->form([
                    Forms\Components\Select::make('award_type_id')
                        ->label('Award Type')
                        ->options(AwardType::all()->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    Forms\Components\Checkbox::make('graduating_confirmation')
                        ->label('I confirm that I am graduating.')
                        ->required()
                        ->accepted(),
                ])
                ->action(function (array $data): void {
                    // Check if user already has a pending application
                    $existingApplication = LeadershipAwardApplication::where('user_id', auth()->id())
                        ->where('award_type_id', $data['award_type_id'])
                        ->whereIn('status', ['pending', 'accepted'])
                        ->exists();

                    if ($existingApplication) {
                        Notification::make()
                            ->title('Application Already Exists')
                            ->body('You already have a pending or accepted application for this award type.')
                            ->warning()
                            ->send();
                        return;
                    }

                    LeadershipAwardApplication::create([
                        'user_id' => auth()->id(),
                        'award_type_id' => $data['award_type_id'],
                        'status' => 'pending',
                    ]);

                    Notification::make()
                        ->title('Application Submitted')
                        ->body('Your leadership award application has been submitted successfully.')
                        ->success()
                        ->send();
                }),
            Action::make('edit_profile')
                ->label('Edit Profile')
                ->color('primary')
                ->url(Filament::getProfileUrl()),
            Action::make('view_certificates')
                ->label('View Certificates')
                ->color('gray')
                ->url(\App\Filament\Resources\Certificates\CertificateResource::getUrl('index')),
        ];
    }
}
