<?php

use App\Features\Evaluations\Enums\RankTier;

it('maps a final score to a tier', function (float $score, RankTier $tier) {
    expect(RankTier::fromScore($score))->toBe($tier);
})->with([
    'gold boundary' => [2.41, RankTier::Gold],
    'just under gold' => [2.409, RankTier::Silver],
    'silver boundary' => [1.81, RankTier::Silver],
    'bronze boundary' => [1.21, RankTier::Bronze],
    'below bronze' => [1.209, RankTier::None],
    'zero' => [0.0, RankTier::None],
]);
