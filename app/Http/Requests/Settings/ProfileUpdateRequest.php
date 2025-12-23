<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'image' => 'nullable|string',
            'telegram' => ['nullable', 'string', 'max:255'],

        ];
    }
    public function messages(): array
    {
        return [
            'first_name.required' => __('setting.profile.validate_error.first_name_required'),
            'first_name.max' => __('setting.profile.validate_error.first_name_max'),

            'last_name.required' => __('setting.profile.validate_error.last_name_required'),
            'last_name.max' => __('setting.profile.validate_error.last_name_max'),

            'email.required' => __('setting.profile.validate_error.email_required'),
            'email.string' => __('setting.profile.validate_error.email_string'),
            'email.lowercase' => __('setting.profile.validate_error.email_lowercase'),
            'email.email' => __('setting.profile.validate_error.email_email'),
            'email.max' => __('setting.profile.validate_error.email_max'),
            'email.unique' => __('setting.profile.validate_error.email_unique'),

            'phone.max' => __('setting.profile.validate_error.phone_max'),

            'gender.required' => __('setting.profile.validate_error.gender_required'),
            'gender.in' => __('setting.profile.validate_error.gender_in'),

            'image.string' => __('setting.profile.validate_error.image_string'),

            'telegram.max' => __('setting.profile.validate_error.telegram_max'),
        ];
    }

}
