<?php

namespace App\Features\Evaluations\Enums;

enum EvaluatorType: string
{
    case Adviser = 'adviser';
    case Peer = 'peer';
    case Self = 'self';

    /**
     * Share of the final score that comes from this evaluator.
     */
    public function weight(): float
    {
        return match ($this) {
            self::Adviser => 0.65,
            self::Peer => 0.25,
            self::Self => 0.10,
        };
    }
}
