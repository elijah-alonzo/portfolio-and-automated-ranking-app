<?php

namespace App\Features\Users\Controllers;

use App\Features\Users\Requests\UpdatePasswordRequest;
use App\Features\Users\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class ProfileController
{
    public function edit(Request $request): Response
    {
        $user = $request->user()->load('department:id,name');

        return Inertia::render('app/Profile', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'contactNumber' => $user->contact_number,
                'bio' => $user->bio,
                'avatar' => $user->avatarUrl(),
                'role' => ucfirst((string) $user->role),
                'department' => $user->department?->name,
            ],
            'maxAvatarKb' => UpdateProfileRequest::AVATAR_MAX_KB,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $details = $request->safe()->only(['name', 'email', 'contact_number', 'bio']);

        if ($request->hasFile('avatar') || $request->boolean('remove_avatar')) {
            if ($user->pfp && ! str_starts_with($user->pfp, 'http')) {
                Storage::disk('public')->delete($user->pfp);
            }

            $details['pfp'] = $request->file('avatar')?->store('profile-pictures', 'public');
        }

        $user->update($details);

        Inertia::flash('success', 'Your details are saved.');

        return back();
    }

    public function password(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => $request->string('password')->value()]);

        // A fresh session id after a credential change.
        $request->session()->regenerate();

        Inertia::flash('success', 'Your password is changed.');

        return back();
    }
}
