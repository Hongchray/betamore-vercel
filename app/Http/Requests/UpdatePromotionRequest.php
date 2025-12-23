<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromotionRequest extends FormRequest
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
            'banner' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_km' => 'nullable|string',
            'type' => 'required|in:percent,flat',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'banner' => 'nullable|string',
            'end_date' => 'required|date|after_or_equal:start_date',
           
            'items' => 'nullable|array',
            'items.*' => 'uuid|exists:items,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name_en.required' => trans('promotion.form_error.name_en_required'),
            'name_en.string' => trans('promotion.form_error.name_en_string'),
            'name_en.max' => trans('promotion.form_error.name_en_max'),

            'name_km.required' => trans('promotion.form_error.name_km_required'),
            'name_km.string' => trans('promotion.form_error.name_km_string'),
            'name_km.max' => trans('promotion.form_error.name_km_max'),

            'description_en.string' => trans('promotion.form_error.description_en_string'),
            'description_km.string' => trans('promotion.form_error.description_km_string'),

            
            'type.required' => trans('promotion.form_error.type_required'),
            'type.in' => trans('promotion.form_error.type_in'),

            'discount_value.required' => trans('promotion.form_error.discount_value_required'),
            'discount_value.numeric' => trans('promotion.form_error.discount_value_numeric'),
            'discount_value.min' => trans('promotion.form_error.discount_value_min'),

            'start_date.required' => trans('promotion.form_error.start_date_required'),
            'start_date.date' => trans('promotion.form_error.start_date_date'),

            'end_date.required' => trans('promotion.form_error.end_date_required'),
            'end_date.date' => trans('promotion.form_error.end_date_date'),
            'end_date.after_or_equal' => trans('promotion.form_error.end_date_after_or_equal'),

            
            'start_time.required' => trans('promotion.form_error.start_time_required'),
            'start_time.date_format' => trans('promotion.form_error.start_time_format'),

            'end_time.required' => trans('promotion.form_error.end_time_required'),
            'end_time.date_format' => trans('promotion.form_error.end_time_format'),
            'end_time.after' => trans('promotion.form_error.end_time_after'),

            'items.array' => trans('promotion.form_error.items_array'),
            'items.*.uuid' => trans('promotion.form_error.items_uuid'),
            'items.*.exists' => trans('promotion.form_error.items_exists'),
        ];
    }
}
