<?php

namespace App\Features\Landing\Controllers;

use App\Features\Landing\Actions\GetLandingStats;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

final class LandingController extends Controller
{
    public function __invoke(GetLandingStats $stats): Response
    {
        return Inertia::render('Home', [
            'stats' => $stats(),
        ]);
    }
}
