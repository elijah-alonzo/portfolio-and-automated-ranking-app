<?php

namespace App\Features\Evaluations\Actions;

use App\Features\Evaluations\Enums\EvaluatorType;

final class CalculateFinalScore
{
    /**
     * Weighted final score, or null until adviser, peer and self scores all exist.
     *
     * @param  array<string, float|int|string>  $scoresByEvaluator  Keyed by EvaluatorType value.
     */
    public function __invoke(array $scoresByEvaluator): ?float
    {
        $total = 0.0;

        foreach (EvaluatorType::cases() as $type) {
            if (! isset($scoresByEvaluator[$type->value])) {
                return null;
            }

            $total += (float) $scoresByEvaluator[$type->value] * $type->weight();
        }

        return round($total, 3);
    }
}
