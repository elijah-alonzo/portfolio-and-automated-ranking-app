<?php

use App\Http\Controllers\EvaluationExportController;
use App\Http\Controllers\EvaluationSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/ranking/login');
});

Route::post('/ranking/my-evaluations/{evaluation}/evaluate/{user}/{type}', [EvaluationSubmissionController::class, 'submit'])
    ->middleware('auth')
    ->name('evaluation.submit');

Route::get('/ranking/admin/evaluations/{evaluation}/evaluate/{user}/{type}/export/{format}', [EvaluationExportController::class, 'export'])
    ->middleware('auth')
    ->name('evaluation.export');
