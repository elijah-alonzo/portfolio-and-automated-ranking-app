<?php

use App\Features\Awards\Models\AwardType;
use App\Features\Awards\Models\LeadershipAwardApplication;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adviser = User::factory()->create(['role' => 'adviser']);
    $this->student = User::factory()->create(['role' => 'student']);
    $this->awardType = AwardType::create(['name' => 'Campus Leadership Award']);
    $this->evaluation = makeEvaluation($this->adviser);
    seatOfficers($this->evaluation, [$this->student], pairPeers: false);
    $this->apply = fn () => $this->actingAs($this->student)->post(route('award-applications.store'), [
        'award_type_id' => $this->awardType->id,
        'graduating' => true,
    ]);
});

it('waits until every evaluation the student is in is completed', function () {
    ($this->apply)();

    expect(LeadershipAwardApplication::count())->toBe(0);
});

it('accepts one application once evaluations are complete', function () {
    $this->evaluation->update(['status' => Evaluation::STATUS_COMPLETED]);

    ($this->apply)();
    ($this->apply)();

    expect(LeadershipAwardApplication::where('user_id', $this->student->id)->count())->toBe(1);
});

it('requires the graduating confirmation', function () {
    $this->evaluation->update(['status' => Evaluation::STATUS_COMPLETED]);

    $this->actingAs($this->student)
        ->post(route('award-applications.store'), ['award_type_id' => $this->awardType->id])
        ->assertSessionHasErrors('graduating');
});
