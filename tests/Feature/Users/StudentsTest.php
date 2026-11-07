<?php

use App\Features\Departments\Models\Department;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adviser = User::factory()->create(['role' => 'adviser']);
});

it('lets an adviser add a student to their department', function () {
    $this->actingAs($this->adviser)->post(route('students.store'), [
        'name' => 'Ana Reyes',
        'email' => 'ana@example.com',
        'password' => 'a-long-password',
        'password_confirmation' => 'a-long-password',
        'is_active' => true,
    ])->assertRedirect();

    expect(User::where('email', 'ana@example.com')->first())
        ->role->toBe('student')
        ->department_id->toBe($this->adviser->department_id);
});

it('lets an adviser edit only their own department\'s students', function () {
    $mine = User::factory()->create(['role' => 'student']);
    $theirs = User::factory()->create(['role' => 'student', 'department_id' => Department::create(['name' => 'Other'])->id]);
    $details = fn (User $student) => ['name' => 'Renamed', 'email' => $student->email, 'is_active' => true];

    $this->actingAs($this->adviser)->put(route('students.update', $mine), $details($mine))->assertRedirect();
    $this->actingAs($this->adviser)->put(route('students.update', $theirs), $details($theirs))->assertForbidden();

    expect($mine->fresh()->name)->toBe('Renamed');
});

it('keeps the students page to advisers', function () {
    $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('students.index'))->assertForbidden();
});

it('lets an adviser open a student\'s portfolio in their department', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->actingAs($this->adviser)->withoutVite()->get(route('students.show', $student))->assertOk();
    $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('students.show', $student))->assertForbidden();
});
