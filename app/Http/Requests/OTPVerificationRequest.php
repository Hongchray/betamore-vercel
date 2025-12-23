<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OTPVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string'],
            'otp_code' => ['required', 'string', 'size:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'otp_code.required' => 'OTP code is required.',
            'otp_code.size' => 'OTP code must be 4 digits.',
        ];
    }
}
