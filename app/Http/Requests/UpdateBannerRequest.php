<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $banner = $this->route('banner');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banners', 'name')->ignore($banner->id),
            ],
            'banner_image' => 'required|string|max:500',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => __('banner.validation.name_required'),
            'name.string'       => __('banner.validation.name_string'),
            'name.max'          => __('banner.validation.name_max'),
            'name.unique'       => __('banner.validation.name_unique'),

            'banner_image.required' => __('banner.validation.image_required'),
            'banner_image.string' => __('banner.validation.image_string'),
            'banner_image.max'    => __('banner.validation.image_max'),

            'description.string' => __('banner.validation.description_string'),
            'description.max'    => __('banner.validation.description_max'),
        ];
    }
}
