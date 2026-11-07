<?php

namespace App\Http\Middleware;

use App\Features\Users\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * Props shared with every page.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            'appUrl' => config('app.url'),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'firstName' => str($user->name)->before(' ')->value(),
                    'email' => $user->email,
                    'avatar' => $user->avatarUrl(),
                    'role' => ucfirst((string) $user->role),
                    'isAdmin' => $user->isAdmin(),
                ] : null,
            ],
            // What the navigation offers. The server still checks every request.
            'can' => $user ? [
                'manageStudents' => Gate::forUser($user)->allows('manageStudents'),
                'portfolio' => $user->isStudent(),
            ] : null,
            // The bell in the header; it polls for just this prop.
            'notifications' => fn () => $user ? [
                'unread' => $user->unreadNotifications()->count(),
                'recent' => NotificationResource::collection($user->notifications()->latest()->limit(8)->get())->resolve($request),
            ] : null,
        ];
    }
}
