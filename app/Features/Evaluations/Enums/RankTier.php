<?php

namespace App\Features\Evaluations\Enums;

enum RankTier: string
{
    case Gold = 'gold';
    case Silver = 'silver';
    case Bronze = 'bronze';
    case None = 'none';

    public static function fromScore(float $score): self
    {
        return match (true) {
            $score >= 2.41 => self::Gold,
            $score >= 1.81 => self::Silver,
            $score >= 1.21 => self::Bronze,
            default => self::None,
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
