<?php

use App\Features\Users\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('opens every admin page for the seeded admin', function (string $path) {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(User::where('role', 'admin')->firstOrFail())
        ->get($path)
        ->assertOk();
})->with([
    '/admin',
    '/admin/users',
    '/admin/users/create',
    '/admin/departments',
    '/admin/councils',
    '/admin/positions',
    '/admin/evaluations',
    '/admin/certificates',
    '/admin/certificates/create',
    '/admin/award-types',
    '/admin/leadership-award-applications',
    '/admin/helps',
    '/admin/profile',
]);

it('seeds only reference data and one admin, and is safe to run twice', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBe(1)
        ->and(User::first()->role)->toBe('admin');
});
