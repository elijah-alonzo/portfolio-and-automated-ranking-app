<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Filament\Resources\Evaluations\Widgets\EvaluationRecommendationsOverview;
use App\Filament\Resources\Evaluations\Widgets\EvaluationSubmissionProgressWidget;
use App\Filament\Resources\Evaluations\Widgets\TopStudentsStatsWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

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

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();
        $councilName = $record->council?->name ?? 'Council';
        $academicYear = $record->academic_year ?? null;

        if ($academicYear) {
            return $councilName.' '.$academicYear;
        }

        return $councilName;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EvaluationRecommendationsOverview::class,
            EvaluationSubmissionProgressWidget::class,
            TopStudentsStatsWidget::class,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
