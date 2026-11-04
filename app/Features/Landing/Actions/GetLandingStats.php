<?php

namespace App\Features\Landing\Actions;

use App\Features\Councils\Models\Council;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Users\Models\User;

final class GetLandingStats
{
    /**
     * @return array{ongoingEvaluations: int, activeCouncils: int, studentOfficers: int}
     */
    public function __invoke(): array
    {
        return [
            'ongoingEvaluations' => Evaluation::query()->where('status', 'ongoing')->count(),
            'activeCouncils' => Council::query()->where('is_active', true)->count(),
            'studentOfficers' => User::query()->where('role', 'student')->count(),
        ];
    }
}
