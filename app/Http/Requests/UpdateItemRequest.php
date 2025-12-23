<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Update with permission check if needed
    }

    public function rules(): array
    {
        return [
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_km' => 'nullable|string',
            'company_id' => 'required|uuid|exists:companies,id',
            'modifications' => 'required|array|min:1',
            'modifications.*.id' => 'nullable|uuid',
            'modifications.*.modification_name' => 'required|string|max:255',
            'modifications.*.unit' => 'required|string|max:50',
            'modifications.*.unit_price' => 'required|numeric|min:0',
            'images' => ['nullable', 'array'],
            'images.*.image' => ['required', 'string'], // or 'url' if URL expected
            'images.*.is_main' => ['required', 'integer', 'in:0,1'],
        ];
    }
    public function messages(): array
    {
        return [
            'name_en.required' => trans('item.error_form.name_en_required'),
            'name_en.string' => trans('item.error_form.name_en_string'),
            'name_en.max' => trans('item.error_form.name_en_max'),


            'name_km.required' => trans('item.error_form.name_km_required'),
            'name_km.string' => trans('item.error_form.name_km_string'),
            'name_km.max' => trans('item.error_form.name_km_max'),

            'company_id.required' => trans('item.error_form.company_id_required'),
            'company_id.uuid' => trans('item.error_form.company_id_uuid'),
            'company_id.exists' => trans('item.error_form.company_id_exists'),

            'modifications.array' => trans('item.error_form.modifications_array'),
            'modifications.*.id.uuid' => trans('item.error_form.modification_id_uuid'),
            'modifications.*.modification_name.required' => trans('item.error_form.modification_name_required'),
            'modifications.*.modification_name.string' => trans('item.error_form.modification_name_string'),
            'modifications.*.modification_name.max' => trans('item.error_form.modification_name_max'),

            'modifications.*.unit.required' => trans('item.error_form.unit_required'),
            'modifications.*.unit.string' => trans('item.error_form.unit_string'),
            'modifications.*.unit.max' => trans('item.error_form.unit_max'),

            'modifications.*.unit_price.required' => trans('item.error_form.unit_price_required'),
            'modifications.*.unit_price.numeric' => trans('item.error_form.unit_price_numeric'),
            'modifications.*.unit_price.min' => trans('item.error_form.unit_price_min'),

            'images.array' => trans('item.error_form.images_array'),
            'images.*.string' => trans('item.error_form.image_string'),
        ];
    }
}
