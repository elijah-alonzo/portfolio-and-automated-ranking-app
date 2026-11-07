<?php

namespace App\Providers;

use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Policies\EvaluationPolicy;
use App\Features\Users\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::policy(Evaluation::class, EvaluationPolicy::class);

        // Advisers keep the student accounts and certificates of their own department.
        Gate::define('manageStudents', fn (User $user) => $user->isAdviser());
        Gate::define('manageStudent', fn (User $user, User $student) => $user->isAdviser()
            && $student->isStudent()
            && $student->department_id === $user->department_id);
    }
}
