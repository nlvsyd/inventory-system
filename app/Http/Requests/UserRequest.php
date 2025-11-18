<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'password' => $userId ? [
                'nullable',
                Rule::when($this->filled('password'), [
                    'string',
                    Password::defaults(),
                ]),
            ] : [
                'required',
                'string',
                Password::defaults(),
            ],
            'role' => ['required', 'string', Rule::in(['System Admin', 'Suppliers', 'Customers'])],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty password to null for updates
        if ($this->route('user')) {
            $password = $this->input('password');
            if ($password === '' || $password === null || empty($password)) {
                $this->merge([
                    'password' => null,
                ]);
            }
        }
    }

    /**
     * Get validated data, excluding password if it's null or empty.
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Remove password from validated data if it's null, empty, or not set (for updates)
        if ($this->route('user')) {
            if (
                ! isset($validated['password']) ||
                $validated['password'] === null ||
                $validated['password'] === '' ||
                empty($validated['password'])
            ) {
                unset($validated['password']);
            }
        }

        return $validated;
    }
}
