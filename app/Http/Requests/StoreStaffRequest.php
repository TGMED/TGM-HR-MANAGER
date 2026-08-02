<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:190', Rule::unique('users', 'email')],
            'employee_id' => ['nullable', 'string', 'max:40', Rule::unique('users', 'employee_id')],
            'phone' => ['nullable', 'string', 'max:30'],
            'department' => ['nullable', 'string', 'max:80'],
            'position' => ['nullable', 'string', 'max:80'],
            'hired_at' => ['nullable', 'date'],
            'role' => ['required', Rule::enum(Role::class)],
            // Staff must belong to a site; admins do not clock in, so theirs
            // is optional and only marks where they are based.
            'location_id' => [
                Rule::requiredIf(fn (): bool => $this->input('role') === Role::Staff->value),
                'nullable',
                'integer',
                Rule::exists('locations', 'id'),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'location_id.required' => 'Pick the work location this person clocks in at.',
        ];
    }
}
