<?php

namespace App\Filament\Resources\MyEvaluations\Widgets;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use App\Models\EvaluationPeerEvaluator;
use Filament\Widgets\ChartWidget;

class MySubmissionProgressWidget extends ChartWidget
{
    public ?Evaluation $record = null;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '200px';

    protected ?string $heading = 'My Submission Progress';

    protected ?string $description = 'Submitted evaluations you are assigned to';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => true,
            'aspectRatio' => 4,
            'layout' => [
                'padding' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ],
            ],
            'cutout' => '65%',
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'boxWidth' => 10,
                    ],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        if (! $this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $user = auth()->user();
        if (! $user) {
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

        $expectedForms = 0;

        if ($this->record->council_adviser_id === $user->id) {
            $expectedForms += count($assignedUserIds);
        }

        if (in_array($user->id, $assignedUserIds, true)) {
            $expectedForms += 1;
        }

        $expectedForms += EvaluationPeerEvaluator::query()
            ->where('evaluation_id', $this->record->id)
            ->where('evaluator_user_id', $user->id)
            ->count();

        $submittedForms = EvaluationForm::query()
            ->where('evaluation_id', $this->record->id)
            ->where('evaluator_id', $user->id)
            ->where('status', 'submitted')
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
