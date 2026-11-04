<?php

use App\Features\Evaluations\Controllers\EvaluationExportController;
use App\Features\Evaluations\Controllers\EvaluationSubmissionController;
use App\Features\Landing\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::middleware('auth')->group(function () {
    Route::post('/ranking/my-evaluations/{evaluation}/evaluate/{user}/{type}', [EvaluationSubmissionController::class, 'submit'])
        ->name('evaluation.submit');

    Route::get('/ranking/admin/evaluations/{evaluation}/evaluate/{user}/{type}/export/{format}', [EvaluationExportController::class, 'export'])
        ->name('evaluation.export');
});
