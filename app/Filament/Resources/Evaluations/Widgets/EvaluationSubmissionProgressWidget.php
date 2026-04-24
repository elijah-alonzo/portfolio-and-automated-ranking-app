<?php

namespace App\Filament\Resources\Evaluations\Widgets;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use Filament\Widgets\ChartWidget;

class EvaluationSubmissionProgressWidget extends ChartWidget
{
    public ?Evaluation $record = null;

    protected int|string|array $columnSpan = 1;

    protected ?string $heading = 'Submission Progress';

    protected ?string $description = 'Submitted evaluations for assigned students';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        if (! $this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $assignedUserIds = $this->record->positionSlots()
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $expectedForms = count($assignedUserIds) * 3;

        $submittedForms = EvaluationForm::query()
            ->where('evaluation_id', $this->record->id)
            ->where('status', 'submitted')
            ->whereIn('user_id', $assignedUserIds)
            ->count();

        $remainingForms = max($expectedForms - $submittedForms, 0);

        return [
            'datasets' => [
                [
                    'label' => 'Submission Progress',
                    'data' => [$submittedForms, $remainingForms],
                    'backgroundColor' => ['#16a34a', '#e5e7eb'],
                ],
            ],
            'labels' => ['Submitted', 'Remaining'],
        ];
    }
}
