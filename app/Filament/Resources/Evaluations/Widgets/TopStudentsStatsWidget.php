<?php

namespace App\Filament\Resources\Evaluations\Widgets;

use App\Models\Evaluation;
use App\Models\EvaluationRank;
use Filament\Widgets\ChartWidget;

class TopStudentsStatsWidget extends ChartWidget
{
    public ?Evaluation $record = null;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '250px';

    protected ?string $heading = 'Top 10 Students';

    protected ?string $description = 'Highest evaluation scores for this council';

    protected function getType(): string
    {
        return 'bar';
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
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 0,
                        'minRotation' => 0,
                        'padding' => 0,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 0.5,
                        'padding' => 0,
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

        $topRanks = EvaluationRank::query()
            ->where('evaluation_id', $this->record->id)
            ->whereNotNull('final_score')
            ->orderByDesc('final_score')
            ->limit(10)
            ->with('user')
            ->get();

        $labels = $topRanks->map(fn ($rank) => $rank->user?->name ?? 'Student')->toArray();
        $scores = $topRanks->map(fn ($rank) => (float) $rank->final_score)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Final Score',
                    'data' => $scores,
                    'backgroundColor' => '#16a34a',
                    'barThickness' => 12,
                    'maxBarThickness' => 12,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
