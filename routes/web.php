<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluationSubmissionController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/ranking/my-evaluations/{evaluation}/evaluate/{user}/{type}', [EvaluationSubmissionController::class, 'submit'])->name('evaluation.submit');
