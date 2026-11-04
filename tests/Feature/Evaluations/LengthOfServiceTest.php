<?php

use App\Features\Awards\Models\AwardType;
use App\Features\Councils\Models\Council;
use App\Features\Evaluations\Models\Evaluation;
use App\Features\Evaluations\Models\EvaluationForm;
use App\Features\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('scores length of service by years served up to the evaluated year', function () {
    $adviser = User::factory()->create(['role' => 'adviser']);
    $student = User::factory()->create(['role' => 'student']);
    $award = AwardType::create(['name' => 'Campus Leadership Award', 'description' => 'Test']);
    $council = Council::create(['name' => 'Test Council', 'code' => 'TC', 'award_type_id' => $award->id]);

    $evaluations = collect(['2023-2024', '2024-2025', '2025-2026'])->map(function (string $year) use ($council, $adviser, $student) {
        $evaluation = Evaluation::create([
            'council_id' => $council->id,
            'council_adviser_id' => $adviser->id,
            'academic_year' => $year,
            'status' => 'completed',
        ]);
        $evaluation->users()->attach($student->id);

        return $evaluation;
    });

    expect(EvaluationForm::getLengthOfServiceData($evaluations[0], $student->id))->toMatchArray(['years' => 1, 'score' => 1])
        ->and(EvaluationForm::getLengthOfServiceData($evaluations[1], $student->id))->toMatchArray(['years' => 2, 'score' => 2])
        ->and(EvaluationForm::getLengthOfServiceData($evaluations[2], $student->id))->toMatchArray(['years' => 3, 'score' => 3]);
});
