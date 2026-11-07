<?php

use App\Features\Certificates\Models\Certificate;
use App\Features\Departments\Models\Department;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->adviser = User::factory()->create(['role' => 'adviser']);
    $this->students = User::factory()->count(2)->create(['role' => 'student']);
    $this->payload = fn (array $ids) => [
        'student_ids' => $ids,
        'name' => 'Leadership Seminar',
        'date_issued' => now()->toDateString(),
        'file' => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
    ];
});

it('issues one PDF to several students and notifies them', function () {
    $this->actingAs($this->adviser)
        ->post(route('certificates.store'), ($this->payload)($this->students->pluck('id')->all()))
        ->assertRedirect();

    expect(Certificate::count())->toBe(2)
        ->and(Certificate::distinct()->count('file_path'))->toBe(1)
        ->and($this->students[0]->notifications()->count())->toBe(1);
});

it('refuses students outside the adviser\'s department', function () {
    $outsider = User::factory()->create(['role' => 'student', 'department_id' => Department::create(['name' => 'Other'])->id]);

    $this->actingAs($this->adviser)
        ->post(route('certificates.store'), ($this->payload)([$outsider->id]))
        ->assertSessionHasErrors('student_ids.0');

    expect(Certificate::count())->toBe(0);
});

it('does not let students issue certificates', function () {
    $this->actingAs($this->students[0])
        ->post(route('certificates.store'), ($this->payload)([$this->students[1]->id]))
        ->assertForbidden();
});
