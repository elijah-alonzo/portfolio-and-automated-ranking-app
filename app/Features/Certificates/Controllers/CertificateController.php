<?php

namespace App\Features\Certificates\Controllers;

use App\Features\Certificates\Models\Certificate;
use App\Features\Certificates\Requests\IssueCertificateRequest;
use App\Features\Users\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Students see the certificates issued to them; advisers issue PDFs to students in their department.
 */
final class CertificateController
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $canIssue = Gate::allows('manageStudents');

        $certificates = Certificate::query()
            ->with('user:id,name,pfp')
            ->when(
                $canIssue,
                fn ($query) => $query->whereHas('user', fn ($students) => $students->where('department_id', $user->department_id)),
                fn ($query) => $query->where('user_id', $user->id),
            )
            ->latest('date_issued')
            ->get()
            ->map(fn (Certificate $certificate) => [
                'id' => $certificate->id,
                'name' => $certificate->certification_name,
                'student' => $certificate->user?->name,
                'studentAvatar' => $certificate->user?->avatarUrl(),
                'dateIssued' => $certificate->date_issued->toDateString(),
                'url' => $certificate->fileUrl(),
            ]);

        return Inertia::render('app/Certificates', [
            'certificates' => $certificates,
            'canIssue' => $canIssue,
            'students' => $canIssue
                ? User::where('role', 'student')->where('department_id', $user->department_id)->orderBy('name')->get(['id', 'name'])
                : [],
            'maxFileKb' => IssueCertificateRequest::FILE_MAX_KB,
        ]);
    }

    public function store(IssueCertificateRequest $request): RedirectResponse
    {
        Gate::authorize('manageStudents');

        // One file, shared by every student it was issued to.
        $path = $request->file('file')->store('certificates', 'public');
        $students = User::whereIn('id', $request->input('student_ids'))->get();

        foreach ($students as $student) {
            Certificate::create([
                'user_id' => $student->id,
                'file_path' => $path,
                'certification_name' => $request->string('name')->value(),
                'date_issued' => $request->date('date_issued'),
            ]);

            Notification::make()
                ->title('Certificate issued')
                ->body('You have been issued a certificate: '.$request->string('name')->value())
                ->success()
                ->sendToDatabase($student);
        }

        Inertia::flash('success', 'Certificate issued to '.$students->count().' student(s).');

        return back();
    }
}
