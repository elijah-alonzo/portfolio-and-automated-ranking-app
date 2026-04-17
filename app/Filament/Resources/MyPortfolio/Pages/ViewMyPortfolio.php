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

    public function mount(): void
    {
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
                ->label('Apply for Award')
                ->color('primary')
                ->visible(fn() => auth()->user()->role === 'student')
                ->modalHeading('Apply for Leadership Award')
                ->modalDescription('Select the award type you are applying for, then confirm your graduation status.')
                ->modalIcon('heroicon-o-academic-cap')
                ->modalIconColor('success')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Submit Application')
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

                    $adminUsers = User::where('role', 'admin')->get();
                    foreach ($adminUsers as $admin) {
                        Notification::make()
                            ->title('New Award Application')
                            ->body(auth()->user()->name . ' submitted a leadership award application.')
                            ->success()
                            ->sendToDatabase($admin);
                    }

                    Notification::make()
                        ->title('Application Submitted')
                        ->body('Your leadership award application has been submitted successfully.')
                        ->success()
                        ->send();
                }),
            Action::make('view_certificates')
                ->label('Issued Certificates')
                ->color('gray')
                ->url(\App\Filament\Resources\Certificates\CertificateResource::getUrl('index')),
        ];
    }
}
