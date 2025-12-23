<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userType = $this->input('type', UserType::CUSTOMER->value);

        return match($userType) {
            UserType::ADMIN->value => $this->getAdminRules(),
            UserType::CUSTOMER->value => $this->getCustomerRules(),
            default => $this->getCustomerRules(),
        };
    }


    private function getAdminRules(): array
    {
        // Try different route parameter names
        $userId = $this->route('user') ?? $this->route('customer') ?? $this->route('id');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'image' => 'nullable|string',
            'phone' => ['nullable', 'string', 'max:255', Rule::unique('users', 'phone')->ignore($userId)],
            'telegram' => ['nullable', 'string', 'max:255', Rule::unique('users', 'telegram')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'type' => ['required', Rule::in(array_column(UserType::cases(), 'value'))],
        ];
    }

   private function getCustomerRules(): array
    {
        // Try different route parameter names
        $userId = $this->route('user') ?? $this->route('customer') ?? $this->route('id');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'image' => 'nullable|string',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['required', 'string', 'max:255', Rule::unique('users', 'phone')->ignore($userId)],
            'telegram' => ['nullable', 'string', 'max:255', Rule::unique('users', 'telegram')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'exists:roles,id'], // Nullable for customers
            'type' => ['required', Rule::in(array_column(UserType::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => __('user.form_error.first_name_required'),
            'last_name.required' => __('user.form_error.last_name_required'),
            'gender.required' => __('user.form_error.gender_required'),
            'gender.in' => __('user.form_error.gender_invalid'),
            'email.required' => __('user.form_error.email_required'),
            'email.email' => __('user.form_error.email_invalid'),
            'email.unique' => __('user.form_error.email_unique'),
            'phone.required' => __('user.form_error.phone_required'),
            'phone.unique' => __('user.form_error.phone_unique'),
            'telegram.unique' => __('user.form_error.telegram_unique'),
            'password.required' => __('user.form_error.password_required'),
            'password.min' => __('user.form_error.password_min'),
            'password.confirmed' => __('user.form_error.password_confirmed'),
            'role_id.required' => __('user.form_error.role_required'),
            'role_id.exists' => __('user.form_error.role_invalid'),
            'type.required' => __('user.form_error.type_required'),
            'type.in' => __('user.form_error.type_invalid'),
        ];
    }
}
