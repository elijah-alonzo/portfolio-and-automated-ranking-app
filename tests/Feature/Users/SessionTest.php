<?php

use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->withoutVite());

it('shows the sign-in page with live stats', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Login')
            ->where('stats.activeCouncils', 0)
            ->where('stats.ongoingEvaluations', 0)
            ->where('stats.studentOfficers', 0));
});

it('sends students and advisers to the app', function (string $role) {
    $user = User::factory()->create(['role' => $role, 'password' => 'secret-pass']);

    $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass'])
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
})->with(['student', 'adviser']);

it('sends admins to the admin panel', function () {
    $admin = User::factory()->create(['role' => 'admin', 'password' => 'secret-pass']);

    $this->post('/login', ['email' => $admin->email, 'password' => 'secret-pass'])
        ->assertRedirect('/admin');
});

it('rejects a wrong password', function () {
    $user = User::factory()->create(['password' => 'secret-pass']);

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('skips the sign-in page when already signed in', function () {
    $this->actingAs(User::factory()->create(['role' => 'student']))
        ->get('/')
        ->assertRedirect('/app');
});

it('signs out', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

it('keeps the admin panel for admins only', function () {
    $this->actingAs(User::factory()->create(['role' => 'adviser']))->get('/admin')->assertForbidden();
    $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin')->assertOk();
});

it('sends guests from the app to sign in', function () {
    $this->get('/app')->assertRedirect('/');
});
