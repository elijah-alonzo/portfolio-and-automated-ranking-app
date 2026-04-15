<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $evaluation = $this->record;

        if (! $evaluation) {
            return;
        }

        $adviser = User::find($evaluation->council_adviser_id);

        if (! $adviser) {
            return;
        }

        Notification::make()
            ->title('New Evaluation Assigned')
            ->body("You were assigned to the {$evaluation->council->name} evaluation for {$evaluation->academic_year}.")
            ->info()
            ->sendToDatabase($adviser);
    }
}
