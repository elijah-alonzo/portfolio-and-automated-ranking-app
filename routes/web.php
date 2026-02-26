<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationSubmissionController;

Route::get('/', function () {
    return redirect('/ranking/login');
});

Route::post('/ranking/my-evaluations/{evaluation}/evaluate/{user}/{type}', [EvaluationSubmissionController::class, 'submit'])
    ->middleware('auth')
    ->name('evaluation.submit');
