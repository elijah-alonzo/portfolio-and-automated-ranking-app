<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationSubmissionController;
use App\Http\Controllers\EvaluationExportController;

Route::get('/', function () {
    return redirect('/ranking/login');
});

Route::post('/ranking/my-evaluations/{evaluation}/evaluate/{user}/{type}', [EvaluationSubmissionController::class, 'submit'])
    ->middleware('auth')
    ->name('evaluation.submit');

Route::get('/ranking/admin/evaluations/{evaluation}/evaluate/{user}/{type}/export/{format}', [EvaluationExportController::class, 'export'])
    ->middleware('auth')
    ->name('evaluation.export');
