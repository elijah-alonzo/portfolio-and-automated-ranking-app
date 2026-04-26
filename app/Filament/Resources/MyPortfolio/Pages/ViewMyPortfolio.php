<?php

namespace App\Filament\Resources\MyPortfolio\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use App\Filament\Resources\MyPortfolio\MyPortfolioResource;
use App\Models\AwardType;
use App\Models\Evaluation;
use App\Models\LeadershipAwardApplication;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
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
        return 'Welcome, '.auth()->user()->name;
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
            'evaluationRanks',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('apply_for_leadership_award')
                ->label(fn (): string => match ($this->getActiveLeadershipAwardApplication()?->status) {
                    'pending' => 'Application Sent',
                    'accepted' => 'Application Accepted',
                    default => 'Apply for Award',
                })
                ->color(fn (): string => match ($this->getActiveLeadershipAwardApplication()?->status) {
                    'pending' => 'warning',
                    'accepted' => 'success',
                    default => 'primary',
                })
                ->disabled(fn (): bool => in_array($this->getActiveLeadershipAwardApplication()?->status, ['pending', 'accepted'], true)
                    || ! $this->hasParticipatedInEvaluations()
                    || $this->hasIncompleteParticipatingEvaluations())
                ->tooltip(fn (): ?string => ! $this->hasParticipatedInEvaluations()
                    ? 'You cannot apply for an award because you have not participated in any evaluations yet.'
                    : ($this->hasIncompleteParticipatingEvaluations()
                        ? 'You cannot apply for an award while you are in an evaluation that is not completed.'
                        : null))
                ->visible(fn () => auth()->user()->role === 'student')
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
                    if (! $this->hasParticipatedInEvaluations()) {
                        Notification::make()
                            ->title('Not Eligible Yet')
                            ->body('You cannot apply for an award because you have not participated in any evaluations yet.')
                            ->warning()
                            ->send();

                        return;
                    }

                    if ($this->hasIncompleteParticipatingEvaluations()) {
                        Notification::make()
                            ->title('Evaluation In Progress')
                            ->body('You cannot apply for an award while you are in an evaluation that is not completed.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $existingApplication = $this->getActiveLeadershipAwardApplication();

                    if ($existingApplication) {
                        Notification::make()
                            ->title('Application Already Exists')
                            ->body('You can only have one pending or accepted leadership award application at a time.')
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
                            ->body(auth()->user()->name.' submitted a leadership award application.')
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
                ->url(CertificateResource::getUrl('index')),
        ];
    }

    protected function getActiveLeadershipAwardApplication(): ?LeadershipAwardApplication
    {
        return LeadershipAwardApplication::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'accepted'])
            ->latest('created_at')
            ->first();
    }

    protected function hasIncompleteParticipatingEvaluations(): bool
    {
        return auth()->user()
            ->participatingEvaluations()
            ->where('status', '!=', Evaluation::STATUS_COMPLETED)
            ->exists();
    }

    protected function hasParticipatedInEvaluations(): bool
    {
        return auth()->user()
            ->participatingEvaluations()
            ->exists();
    }
}
