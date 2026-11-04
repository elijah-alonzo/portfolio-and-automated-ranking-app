<?php

namespace App\Features\Evaluations\Requests;

use App\Features\Evaluations\Enums\EvaluatorType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SubmitEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $evaluation = $this->route('evaluation');
        $evaluatee = $this->route('user');

        return match ($this->route('type')) {
            EvaluatorType::Adviser => $evaluation->council_adviser_id === $user?->id,
            EvaluatorType::Self => $evaluatee->id === $user?->id,
            EvaluatorType::Peer => true,
            default => false,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'answers' => ['array'],
            'submission_action' => ['nullable', Rule::in(['draft', 'submitted'])],
        ];
    }

    public function isDraft(): bool
    {
        return $this->input('submission_action') === 'draft';
    }

    /**
     * @return array<string, mixed>
     */
    public function answers(): array
    {
        return $this->input('answers', []);
    }
}
