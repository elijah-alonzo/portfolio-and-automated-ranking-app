<?php

use App\Features\Evaluations\Actions\CalculateFinalScore;

it('weights adviser 65, peer 25 and self 10 percent', function () {
    $score = (new CalculateFinalScore)(['adviser' => 3, 'peer' => 2, 'self' => 1]);

    expect($score)->toBe(2.55);
});

it('returns null until all three evaluators have scored', function (array $scores) {
    expect((new CalculateFinalScore)($scores))->toBeNull();
})->with([
    'nothing' => [[]],
    'adviser only' => [['adviser' => 3]],
    'missing self' => [['adviser' => 3, 'peer' => 3]],
]);

it('rounds to three decimals', function () {
    $score = (new CalculateFinalScore)(['adviser' => 2.333, 'peer' => 2.111, 'self' => 1.999]);

    expect($score)->toBe(round(2.333 * 0.65 + 2.111 * 0.25 + 1.999 * 0.10, 3));
});
