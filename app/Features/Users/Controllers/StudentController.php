<?php

namespace App\Features\Users\Controllers;

use App\Features\Users\Models\User;
use App\Features\Users\Requests\StudentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Advisers keep the student accounts of their own department.
 */
final class StudentController
{
    public function index(Request $request): Response
    {
        Gate::authorize('manageStudents');

        $adviser = $request->user()->load('department:id,name');

        return Inertia::render('app/Students', [
            'department' => $adviser->department?->name,
            'students' => User::where('role', 'student')
                ->where('department_id', $adviser->department_id)
                ->orderBy('name')
                ->get()
                ->map(fn (User $student) => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'contactNumber' => $student->contact_number,
                    'isActive' => (bool) $student->is_active,
                    'avatar' => $student->avatarUrl(),
                ]),
        ]);
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        Gate::authorize('manageStudents');

        $student = User::create([
            ...$request->details(),
            'role' => 'student',
            'department_id' => $request->user()->department_id,
        ]);

        Inertia::flash('success', "{$student->name} can now sign in.");

        return back();
    }

    public function update(StudentRequest $request, User $student): RedirectResponse
    {
        Gate::authorize('manageStudent', $student);

        $student->update($request->details());

        Inertia::flash('success', "{$student->name}'s details are saved.");

        return back();
    }
}
