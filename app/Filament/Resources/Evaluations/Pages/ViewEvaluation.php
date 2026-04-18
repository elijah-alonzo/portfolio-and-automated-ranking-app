<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Filament\Resources\Evaluations\Widgets\EvaluationRecommendationsOverview;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        if ($record->status === 'completed') {
            return [];
        }

        return [
            EditAction::make()
                ->label('Edit Evaluation'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EvaluationRecommendationsOverview::class,
        ];
    }
}
