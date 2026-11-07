<?php

namespace App\Features\Certificates\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class IssueCertificateRequest extends FormRequest
{
    public const FILE_MAX_KB = 10240;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $adviser = $this->user();

        return [
            'student_ids' => ['required', 'array', 'min:1'],
            // Advisers issue only to students in their own department.
            'student_ids.*' => ['integer', Rule::exists('users', 'id')->where('role', 'student')->where('department_id', $adviser->department_id)],
            'name' => ['required', 'string', 'max:255'],
            'date_issued' => ['required', 'date', 'before_or_equal:today'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:'.self::FILE_MAX_KB],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_ids.required' => 'Choose at least one student.',
            'student_ids.*.exists' => 'You can only issue certificates to students in your department.',
            'file.mimes' => 'The certificate must be a PDF.',
        ];
    }
}
