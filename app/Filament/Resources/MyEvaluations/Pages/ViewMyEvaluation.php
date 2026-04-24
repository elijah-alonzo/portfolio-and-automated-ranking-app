<?php

namespace App\Filament\Resources\MyEvaluations\Pages;

use App\Filament\Resources\MyEvaluations\MyEvaluationResource;
use App\Filament\Resources\MyEvaluations\Widgets\CouncilSubmissionProgressWidget;
use App\Filament\Resources\MyEvaluations\Widgets\MySubmissionProgressWidget;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewMyEvaluation extends ViewRecord
{
    protected static string $resource = MyEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CouncilSubmissionProgressWidget::class,
            MySubmissionProgressWidget::class,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function getTitle(): string|Htmlable
    {
        $record = $this->getRecord();
        $councilName = $record->council?->name ?? 'My Council';
        $academicYear = $record->academic_year ?? null;

        if ($academicYear) {
            return $councilName.' '.$academicYear;
        }

        return $councilName;
    }

    protected function getPeerEvaluationActions($record, array $peerEvaluateeIds): array
    {
        $actions = [];

        $evaluatees = $record->users()
            ->whereIn('user_id', $peerEvaluateeIds)
            ->get();

        foreach ($evaluatees as $evaluatee) {
            $actions[] = Action::make("evaluate_peer_{$evaluatee->id}")
                ->label("Evaluate {$evaluatee->name}")
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn () => MyEvaluationResource::getUrl('evaluate-student', ['evaluation' => $record->id, 'user' => $evaluatee->id, 'type' => 'peer']))
                ->tooltip("Complete peer evaluation for {$evaluatee->name}");
        }

        return $actions;
    }
}
