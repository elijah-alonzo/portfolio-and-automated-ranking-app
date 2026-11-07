<?php

use App\Features\Councils\Models\Council;
use App\Features\Councils\Models\CouncilPosition;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Positions\Models\Position;
use App\Features\Users\Models\User;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/*
|--------------------------------------------------------------------------
| Council helpers
|--------------------------------------------------------------------------
*/

/**
 * A council evaluation advised by $adviser, with one slot for each of $positions.
 */
function makeEvaluation(User $adviser, int $positions = 3, string $status = Evaluation::STATUS_CLOSED): Evaluation
{
    $council = Council::create(['name' => 'Test Council', 'code' => 'TC', 'is_active' => true]);

    foreach (range(1, $positions) as $index) {
        $position = Position::create(['title' => "Position {$index}", 'branch' => 'Executive']);
        CouncilPosition::create(['council_id' => $council->id, 'position_id' => $position->id, 'max_slots' => 1, 'is_active' => true]);
    }

    return Evaluation::create([
        'council_id' => $council->id,
        'council_adviser_id' => $adviser->id,
        'academic_year' => '2025-2026',
        'status' => $status,
    ]);
}

/**
 * Seats each student in the next empty slot, then pairs them in a ring as each other's peer evaluators.
 *
 * @param  list<User>  $students
 */
function seatOfficers(Evaluation $evaluation, array $students, bool $pairPeers = true): void
{
    $slots = $evaluation->positionSlots()->with('position')->orderBy('id')->get();

    foreach ($students as $index => $student) {
        $slots[$index]->update(['user_id' => $student->id]);
        $evaluation->users()->attach($student->id, ['position' => $slots[$index]->position->title]);
    }

    if (! $pairPeers) {
        return;
    }

    foreach ($students as $index => $student) {
        EvaluationPeerEvaluator::create([
            'evaluation_id' => $evaluation->id,
            'evaluatee_user_id' => $student->id,
            'evaluator_user_id' => $students[($index + 1) % count($students)]->id,
            'assigned_by_user_id' => $evaluation->council_adviser_id,
            'assigned_at' => now(),
        ]);
    }
}
