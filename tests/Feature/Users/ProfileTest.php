<?php

use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('updates details and photo', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('profile.update'), [
        'name' => 'New Name',
        'email' => $user->email,
        'bio' => 'Hello',
        'avatar' => UploadedFile::fake()->image('me.jpg'),
    ])->assertRedirect();

    $user->refresh();
    expect($user->name)->toBe('New Name')->and($user->pfp)->not->toBeNull();
    Storage::disk('public')->assertExists($user->pfp);
});

it('changes the password only with the current one', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    $this->actingAs($user)->put(route('profile.password'), [
        'current_password' => 'wrong',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertSessionHasErrors('current_password');

    $this->actingAs($user)->put(route('profile.password'), [
        'current_password' => 'old-password',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ]);

    expect(Hash::check('a-new-password', $user->fresh()->password))->toBeTrue();
});
