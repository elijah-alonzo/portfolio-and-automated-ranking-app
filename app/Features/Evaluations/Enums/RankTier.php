<?php

namespace App\Features\Evaluations\Enums;

enum RankTier: string
{
    case Gold = 'gold';
    case Silver = 'silver';
    case Bronze = 'bronze';
    case None = 'none';

    /**
     * Highest final score an officer can reach.
     */
    public const MAX_SCORE = 3.0;

    public static function fromScore(float $score): self
    {
        foreach ([self::Gold, self::Silver, self::Bronze] as $tier) {
            if ($score >= $tier->threshold()) {
                return $tier;
            }
        }

        return self::None;
    }

    /**
     * Lowest final score that earns this tier.
     */
    public function threshold(): float
    {
        return match ($this) {
            self::Gold => 2.41,
            self::Silver => 1.81,
            self::Bronze => 1.21,
            self::None => 0.0,
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Gold => 'warning',
            self::Silver => 'gray',
            self::Bronze => 'orange',
            self::None => 'danger',
        };
    }
}
