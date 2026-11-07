<?php

use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adviser = User::factory()->create(['role' => 'adviser']);
    $this->student = User::factory()->create(['role' => 'student']);
    $this->other = User::factory()->create(['role' => 'student']);
    $this->outsider = User::factory()->create(['role' => 'student']);

    $this->evaluation = makeEvaluation($this->adviser, status: Evaluation::STATUS_ONGOING);
    // Ring pairing: student is evaluated by other, other by student.
    seatOfficers($this->evaluation, [$this->student, $this->other]);

    $this->url = fn (User $evaluatee, string $type) => route('councils.forms.store', [$this->evaluation, $evaluatee, $type]);
    $this->selfAnswers = collect(EvaluationForm::getQuestionsForEvaluator('self'))->map(fn () => 2)->all();
});

it('saves a self evaluation draft', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'draft', 'answers' => []])
        ->assertRedirect();

    expect(EvaluationForm::first())
        ->status->toBe('draft')
        ->evaluator_type->toBe('self');
});

it('submits a complete self evaluation and scores it', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'submitted', 'answers' => $this->selfAnswers])
        ->assertRedirect(route('councils.show', $this->evaluation));

    expect(EvaluationForm::first())
        ->status->toBe('submitted')
        ->evaluator_score->toEqual('2.000');
});

it('rejects a submit with unanswered questions', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'submitted', 'answers' => []]);

    expect(EvaluationForm::count())->toBe(0);
});

it('locks a form once it is submitted', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'submitted', 'answers' => $this->selfAnswers]);

    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'draft', 'answers' => []]);

    expect(EvaluationForm::first()->status)->toBe('submitted');
});

it('only takes forms while the evaluation is open', function () {
    $this->evaluation->update(['status' => Evaluation::STATUS_CLOSED]);

    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'draft', 'answers' => []]);

    expect(EvaluationForm::count())->toBe(0);
});

it('blocks self evaluation of someone else', function () {
    $this->actingAs($this->other)
        ->post(($this->url)($this->student, 'self'), ['answers' => []])
        ->assertForbidden();
});

it('blocks self evaluation by someone who is not an officer', function () {
    $this->actingAs($this->outsider)
        ->post(($this->url)($this->outsider, 'self'), ['answers' => []])
        ->assertForbidden();
});

it('only lets the paired peer write a peer evaluation', function () {
    $this->actingAs($this->other)
        ->post(($this->url)($this->student, 'peer'), ['submission_action' => 'draft', 'answers' => []])
        ->assertRedirect();

    $this->actingAs($this->outsider)
        ->post(($this->url)($this->student, 'peer'), ['submission_action' => 'draft', 'answers' => []])
        ->assertForbidden();
});

it('only lets the assigned adviser submit an adviser evaluation', function () {
    $this->actingAs($this->other)
        ->post(($this->url)($this->student, 'adviser'), ['submission_action' => 'draft', 'answers' => []])
        ->assertForbidden();

    $this->actingAs(User::factory()->create(['role' => 'adviser']))
        ->post(($this->url)($this->student, 'adviser'), ['submission_action' => 'draft', 'answers' => []])
        ->assertForbidden();
});

it('returns 404 for an unknown evaluator type', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'boss'), [])
        ->assertNotFound();
});

it('lets the adviser read a submitted peer form but not an officer read someone else\'s', function () {
    $show = fn (string $type) => route('councils.forms.show', [$this->evaluation, $this->student, $type]);

    $this->actingAs($this->adviser)->withoutVite()->get($show('self'))->assertOk();
    $this->actingAs($this->outsider)->get($show('self'))->assertForbidden();
});
