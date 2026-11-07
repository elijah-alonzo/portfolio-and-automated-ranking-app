<?php

use App\Features\Departments\Models\Department;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Evaluations\Models\EvaluationPeerEvaluator;
use App\Features\Evaluations\Models\EvaluationRank;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->adviser = User::factory()->create(['role' => 'adviser']);
    $this->students = User::factory()->count(3)->create(['role' => 'student']);
    $this->evaluation = makeEvaluation($this->adviser);
});

it('lets the adviser fill a position', function () {
    $slot = $this->evaluation->positionSlots()->first();

    $this->actingAs($this->adviser)
        ->put(route('councils.slots.assign', [$this->evaluation, $slot]), ['student_id' => $this->students[0]->id])
        ->assertRedirect();

    expect($slot->fresh()->user_id)->toBe($this->students[0]->id)
        ->and($this->evaluation->users()->whereKey($this->students[0]->id)->exists())->toBeTrue();
});

it('will not seat one student in two positions', function () {
    [$first, $second] = $this->evaluation->positionSlots()->get();
    $first->update(['user_id' => $this->students[0]->id]);

    $this->actingAs($this->adviser)
        ->put(route('councils.slots.assign', [$this->evaluation, $second]), ['student_id' => $this->students[0]->id]);

    expect($second->fresh()->user_id)->toBeNull();
});

it('only offers students from the council\'s departments', function () {
    $elsewhere = Department::create(['name' => 'Elsewhere']);
    $this->evaluation->council->departments()->attach($this->students[0]->department_id);
    $outsider = User::factory()->create(['role' => 'student', 'department_id' => $elsewhere->id]);
    $slot = $this->evaluation->positionSlots()->first();

    $this->actingAs($this->adviser)
        ->put(route('councils.slots.assign', [$this->evaluation, $slot]), ['student_id' => $outsider->id]);

    expect($slot->fresh()->user_id)->toBeNull();
});

it('pairs a peer evaluator', function () {
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]], pairPeers: false);

    $this->actingAs($this->adviser)
        ->put(route('councils.officers.peer', [$this->evaluation, $this->students[0]]), ['evaluator_id' => $this->students[1]->id]);

    expect(EvaluationPeerEvaluator::where('evaluatee_user_id', $this->students[0]->id)->value('evaluator_user_id'))
        ->toBe($this->students[1]->id);
});

it('removes an officer and their pairings', function () {
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]]);
    $slot = $this->evaluation->positionSlots()->where('user_id', $this->students[0]->id)->first();

    $this->actingAs($this->adviser)->delete(route('councils.slots.remove', [$this->evaluation, $slot]));

    expect($slot->fresh()->user_id)->toBeNull()
        ->and(EvaluationPeerEvaluator::count())->toBe(0)
        ->and($this->evaluation->users()->count())->toBe(1);
});

it('will not open an evaluation until every officer has a peer', function () {
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]], pairPeers: false);

    $this->actingAs($this->adviser)->put(route('councils.status', $this->evaluation), ['status' => 'ongoing']);

    expect($this->evaluation->fresh()->status)->toBe(Evaluation::STATUS_CLOSED);
});

it('opens a ready evaluation and tells the officers', function () {
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]]);

    $this->actingAs($this->adviser)->put(route('councils.status', $this->evaluation), ['status' => 'ongoing']);

    expect($this->evaluation->fresh()->status)->toBe(Evaluation::STATUS_ONGOING)
        ->and($this->students[0]->notifications()->count())->toBe(1);
});

it('completes only once every form is in, and ranks the officers', function () {
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]]);
    $this->evaluation->update(['status' => Evaluation::STATUS_ONGOING]);

    $this->actingAs($this->adviser)->put(route('councils.status', $this->evaluation), ['status' => 'completed']);
    expect($this->evaluation->fresh()->status)->toBe(Evaluation::STATUS_ONGOING);

    foreach ([$this->students[0], $this->students[1]] as $officer) {
        foreach (['self', 'peer', 'adviser'] as $type) {
            EvaluationForm::create([
                'evaluation_id' => $this->evaluation->id,
                'user_id' => $officer->id,
                'evaluator_type' => $type,
                'evaluator_id' => $this->adviser->id,
                'answers' => ['q' => 3],
                'status' => 'submitted',
            ]);
        }
    }

    $this->actingAs($this->adviser)->put(route('councils.status', $this->evaluation), ['status' => 'completed']);

    expect($this->evaluation->fresh()->status)->toBe(Evaluation::STATUS_COMPLETED)
        ->and(EvaluationRank::where('user_id', $this->students[0]->id)->first()->rank->value)->toBe('gold');
});

it('emails reminders for missing self and peer forms', function () {
    Mail::fake();
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]]);
    $this->evaluation->update(['status' => Evaluation::STATUS_ONGOING]);

    $this->actingAs($this->adviser)
        ->post(route('councils.officers.remind', [$this->evaluation, $this->students[0]]))
        ->assertRedirect();
});

it('keeps council controls to its adviser', function () {
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]]);
    $slot = $this->evaluation->positionSlots()->first();

    $this->actingAs($this->students[0])->put(route('councils.status', $this->evaluation), ['status' => 'ongoing'])->assertForbidden();
    $this->actingAs($this->students[0])->delete(route('councils.slots.remove', [$this->evaluation, $slot]))->assertForbidden();
    $this->actingAs(User::factory()->create(['role' => 'adviser']))
        ->put(route('councils.officers.peer', [$this->evaluation, $this->students[0]]), ['evaluator_id' => $this->students[1]->id])
        ->assertForbidden();
});

it('shows the council to its officers but not to others', function () {
    seatOfficers($this->evaluation, [$this->students[0], $this->students[1]]);

    $this->actingAs($this->students[0])
        ->get(route('councils.show', $this->evaluation))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('app/Council')
            ->where('council.canManage', false)
            ->has('students', 0));

    $this->actingAs($this->students[2])->get(route('councils.show', $this->evaluation))->assertForbidden();
});

it('gives the adviser the student list while setting up', function () {
    $this->actingAs($this->adviser)
        ->get(route('councils.show', $this->evaluation))
        ->assertInertia(fn (Assert $page) => $page
            ->where('council.canManage', true)
            ->has('officers', 3)
            ->has('students', 3));
});
