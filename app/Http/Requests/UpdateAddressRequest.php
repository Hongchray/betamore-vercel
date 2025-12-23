<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ensure user is authorized (add logic if needed)
    }

    public function rules(): array
    {
        return [
            'contact_name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:500',
            'city' => 'sometimes|required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'sometimes|required|string|max:20',
            'lattitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];
    }
}
