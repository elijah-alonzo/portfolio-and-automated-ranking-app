<?php

use App\Features\Evaluations\Models\Evaluation;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->adviser = User::factory()->create(['role' => 'adviser']);
    $this->student = User::factory()->create(['role' => 'student']);
    $this->peer = User::factory()->create(['role' => 'student']);
    $this->evaluation = makeEvaluation($this->adviser, status: Evaluation::STATUS_ONGOING);
    seatOfficers($this->evaluation, [$this->student, $this->peer]);
});

it('lists a student\'s open self and peer forms on home', function () {
    $this->actingAs($this->student)
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('app/Home')
            ->has('tasks', 2)
            ->where('tasks.0.type', 'self'));
});

it('lists an adviser form for every officer on the adviser\'s home', function () {
    $this->actingAs($this->adviser)
        ->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->has('tasks', 2)->where('tasks.0.type', 'adviser'));
});

it('renders every app page', function (string $route, string $component, string $as) {
    $user = $as === 'adviser' ? $this->adviser : $this->student;

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    ['councils.index', 'app/Councils', 'student'],
    ['portfolio', 'app/Portfolio', 'student'],
    ['certificates.index', 'app/Certificates', 'student'],
    ['certificates.index', 'app/Certificates', 'adviser'],
    ['students.index', 'app/Students', 'adviser'],
    ['profile.edit', 'app/Profile', 'student'],
]);

it('opens the rubric for the student\'s own self evaluation', function () {
    $this->actingAs($this->student)
        ->get(route('councils.forms.show', [$this->evaluation, $this->student, 'self']))
        ->assertInertia(fn (Assert $page) => $page
            ->component('app/EvaluationForm')
            ->where('isEditable', true)
            ->where('lengthOfService', null)
            ->has('domains', 2));
});

it('fills in length of service on the adviser form', function () {
    $this->actingAs($this->adviser)
        ->get(route('councils.forms.show', [$this->evaluation, $this->student, 'adviser']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('isEditable', true)
            ->whereNot('lengthOfService', null)
            ->has('domains', 4));
});
