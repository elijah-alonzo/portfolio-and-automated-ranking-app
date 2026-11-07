<?php

namespace App\Features\Evaluations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

final class SubmitEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('fill', [$this->route('evaluation'), $this->route('user'), $this->route('type')]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'answers' => ['array'],
            'answers.*' => ['nullable', 'integer', 'between:0,3'],
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
