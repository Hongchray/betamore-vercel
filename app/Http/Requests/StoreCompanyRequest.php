<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'description_en' => 'nullable|string|max:1000',
            'description_km' => 'nullable|string|max:1000',
            'logo' => 'nullable|string|max:255',
        ];
    }
    public function messages(): array
    {
        return [
            'company_id.required' => __('company.validation.company_id.required'),
            'company_id.string' => __('company.validation.company_id.string'),
            'company_id.max' => __('company.validation.company_id.max'),
            'company_id.unique' => __('company.validation.company_id.unique'),

            'name_en.required' => __('company.validation.name_en.required'),
            'name_en.string' => __('company.validation.name_en.string'),
            'name_en.max' => __('company.validation.name_en.max'),

            'name_km.required' => __('company.validation.name_km.required'),
            'name_km.string' => __('company.validation.name_km.string'),
            'name_km.max' => __('company.validation.name_km.max'),

            'description_en.string' => __('company.validation.description_en.string'),
            'description_en.max' => __('company.validation.description_en.max'),

            'description_km.string' => __('company.validation.description_km.string'),
            'description_km.max' => __('company.validation.description_km.max'),

            'logo.string' => __('company.validation.logo.string'),
            'logo.max' => __('company.validation.logo.max'),
        ];
    }

}
