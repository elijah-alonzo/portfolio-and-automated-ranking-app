<?php

namespace App\Features\Users\Requests;

use App\Features\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * An adviser adding or editing a student in their own department.
 */
final class StudentRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        $isNew = ! $student instanceof User;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($isNew ? null : $student->id)],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'password' => [$isNew ? 'required' : 'nullable', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function details(): array
    {
        $details = $this->safe()->only(['name', 'email', 'contact_number']);
        $details['is_active'] = $this->boolean('is_active', true);

        if ($this->filled('password')) {
            $details['password'] = $this->string('password')->value();
        }

        return $details;
    }
}
