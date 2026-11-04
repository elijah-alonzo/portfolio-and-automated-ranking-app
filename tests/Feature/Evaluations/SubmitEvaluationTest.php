<?php

use App\Features\Councils\Models\Council;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adviser = User::factory()->create(['role' => 'adviser']);
    $this->student = User::factory()->create(['role' => 'student']);
    $this->other = User::factory()->create(['role' => 'student']);

    $council = Council::create(['name' => 'Test Council', 'code' => 'TC']);
    $this->evaluation = Evaluation::create([
        'council_id' => $council->id,
        'council_adviser_id' => $this->adviser->id,
        'academic_year' => '2025-2026',
        'status' => 'ongoing',
    ]);

    $this->url = fn (User $evaluatee, string $type) => route('evaluation.submit', [$this->evaluation, $evaluatee, $type]);
});

it('saves a self evaluation draft', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'draft', 'answers' => ['q1' => 2]])
        ->assertSessionHas('success');

    expect(EvaluationForm::first())
        ->status->toBe('draft')
        ->evaluator_type->toBe('self');
});

it('rejects a submit with unanswered questions', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'self'), ['submission_action' => 'submitted', 'answers' => []])
        ->assertSessionHas('error');

    expect(EvaluationForm::count())->toBe(0);
});

it('blocks self evaluation of someone else', function () {
    $this->actingAs($this->other)
        ->post(($this->url)($this->student, 'self'), ['answers' => []])
        ->assertForbidden();
});

it('only lets the assigned adviser submit an adviser evaluation', function () {
    $this->actingAs($this->other)
        ->post(($this->url)($this->student, 'adviser'), ['submission_action' => 'draft', 'answers' => []])
        ->assertForbidden();
});

it('returns 404 for an unknown evaluator type', function () {
    $this->actingAs($this->student)
        ->post(($this->url)($this->student, 'boss'), [])
        ->assertNotFound();
});
