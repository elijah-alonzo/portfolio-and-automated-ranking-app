<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use App\Models\EvaluationForm;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class CouncilSubmissionProgressWidget extends ChartWidget
{
    public ?Evaluation $record = null;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '200px';

    protected ?string $heading = 'Council Submission Progress';

    protected ?string $description = 'Submitted evaluations for your councils';

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
        $user = auth()->user();
        if (! $user) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        if ($this->record) {
            $assignedUserIds = $this->record->positionSlots()
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();

            $expectedForms = count($assignedUserIds) * 3;

            $submittedForms = empty($assignedUserIds)
                ? 0
                : EvaluationForm::query()
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

        $evaluations = Evaluation::query()
            ->whereIn('status', ['closed', 'ongoing'])
            ->where(function (Builder $query) use ($user) {
                $query->where('council_adviser_id', $user->id)
                    ->orWhereHas('users', fn (Builder $subQuery) => $subQuery->where('user_id', $user->id));
            })
            ->get();

        $expectedForms = 0;
        $submittedForms = 0;

        foreach ($evaluations as $evaluation) {
            $assignedUserIds = $evaluation->positionSlots()
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();

            $expectedForms += count($assignedUserIds) * 3;

            if (! empty($assignedUserIds)) {
                $submittedForms += EvaluationForm::query()
                    ->where('evaluation_id', $evaluation->id)
                    ->where('status', 'submitted')
                    ->whereIn('user_id', $assignedUserIds)
                    ->count();
            }
        }

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
