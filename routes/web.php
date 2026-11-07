<?php

use App\Features\Awards\Controllers\AwardApplicationController;
use App\Features\Certificates\Controllers\CertificateController;
use App\Features\Evaluations\Controllers\CouncilController;
use App\Features\Evaluations\Controllers\CouncilStatusController;
use App\Features\Evaluations\Controllers\EvaluationExportController;
use App\Features\Evaluations\Controllers\EvaluationFormController;
use App\Features\Evaluations\Controllers\OfficerController;
use App\Features\Home\Controllers\HomeController;
use App\Features\Users\Controllers\NotificationController;
use App\Features\Users\Controllers\PortfolioController;
use App\Features\Users\Controllers\ProfileController;
use App\Features\Users\Controllers\SessionController;
use App\Features\Users\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

    Route::prefix('app')->group(function () {
        Route::get('/', HomeController::class)->name('home');

        Route::get('councils', [CouncilController::class, 'index'])->name('councils.index');

        Route::prefix('councils/{evaluation}')->name('councils.')->group(function () {
            Route::get('/', [CouncilController::class, 'show'])->name('show');
            Route::put('status', CouncilStatusController::class)->name('status');

            Route::put('slots/{positionSlot}', [OfficerController::class, 'assign'])->scopeBindings()->name('slots.assign');
            Route::delete('slots/{positionSlot}', [OfficerController::class, 'remove'])->scopeBindings()->name('slots.remove');
            Route::put('officers/{officer}/peer', [OfficerController::class, 'peer'])->name('officers.peer');
            Route::post('officers/{officer}/reminders', [OfficerController::class, 'remind'])->name('officers.remind');

            Route::get('forms/{user}/{type}', [EvaluationFormController::class, 'show'])->name('forms.show');
            Route::post('forms/{user}/{type}', [EvaluationFormController::class, 'store'])->name('forms.store');
            Route::get('forms/{user}/{type}/export/{format}', [EvaluationExportController::class, 'export'])->name('forms.export');
        });

        Route::get('portfolio', [PortfolioController::class, 'mine'])->name('portfolio');
        Route::post('award-applications', [AwardApplicationController::class, 'store'])->name('award-applications.store');

        Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::post('certificates', [CertificateController::class, 'store'])->name('certificates.store');

        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');
        Route::get('students/{student}', [PortfolioController::class, 'student'])->name('students.show');
        Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        // POST, not PATCH: a photo upload needs a multipart body.
        Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');

        Route::post('notifications/read', [NotificationController::class, 'readAll'])->name('notifications.read');
        Route::post('notifications/{notification}/open', [NotificationController::class, 'open'])->name('notifications.open');
    });
});
