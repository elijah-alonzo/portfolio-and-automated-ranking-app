<?php

namespace App\Filament\Resources\Users\Pages;

use App\Features\Users\Models\User;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewUserPortfolio extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'portfolio.admin-portfolio-view';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! $this->record instanceof User || ! $this->record->isStudent()) {
            $this->redirect(UserResource::getUrl('edit', ['record' => $this->record]));

            return;
        }

        $this->record->loadMissing([
            'participatingEvaluations.council.awardType',
            'participatingEvaluations.adviser',
            'certificates',
            'evaluationRanks',
        ]);
    }

    public function getHeading(): string|Htmlable
    {
        return $this->record instanceof User ? $this->record->name."'s Portfolio" : 'Student Portfolio';
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (UserResource::canEdit($this->record)) {
            $actions[] = Action::make('editUser')
                ->label('Edit User')
                ->color('info')
                ->url(UserResource::getUrl('edit', ['record' => $this->record]));
        }

        return $actions;
    }
}
