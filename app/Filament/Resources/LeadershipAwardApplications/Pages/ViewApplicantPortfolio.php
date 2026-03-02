<?php

namespace App\Filament\Resources\LeadershipAwardApplications\Pages;

use App\Filament\Resources\LeadershipAwardApplications\LeadershipAwardApplicationResource;
use App\Models\User;
use App\Models\LeadershipAwardApplication;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewApplicantPortfolio extends Page
{
    protected static string $resource = LeadershipAwardApplicationResource::class;

    protected static ?string $title = 'Applicant Portfolio';
    
    protected static bool $shouldRegisterNavigation = false;
    
    protected string $view = 'Portfolio.AdminPortfolioView';

    public ?User $record = null;

    public function getHeading(): string|Htmlable
    {
        return $this->record ? $this->record->name . '\'s Portfolio' : 'Applicant Portfolio';
    }

    public function mount(int|string $application): void
    {
        $app = LeadershipAwardApplication::findOrFail($application);
        
        $this->record = User::with([
            'participatingEvaluations.council.awardType', 
            'participatingEvaluations.adviser', 
            'certificates',
            'evaluationRanks'
        ])->findOrFail($app->user_id);
    }
}
