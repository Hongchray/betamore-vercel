<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => 'required|string|min:3|max:100',
            'logo_url' => 'required|string|max:500',
            'favicon_url' => 'required|string|max:500',
            'meta_description' => 'required|string|min:10|max:255',
            'prefix' => 'required|array',
            'prefix.*' => 'required|string|min:1|max:4',
        ];
    }

    public function messages(): array
    {
        return [
            'site_name.required' => trans('setting.site_info.validation.site_name_required'),
            'site_name.min' => trans('setting.site_info.validation.site_name_min', ['min' => 3]),
            'site_name.max' => trans('setting.site_info.validation.site_name_max', ['max' => 100]),

            'logo_url.required' => trans('setting.site_info.validation.logo_url_required'),
            'logo_url.url' => trans('setting.site_info.validation.logo_url_url'),
            'logo_url.max' => trans('setting.site_info.validation.logo_url_max', ['max' => 500]),

            'favicon_url.required' => trans('setting.site_info.validation.favicon_url_required'),
            'favicon_url.url' => trans('setting.site_info.validation.favicon_url_url'),
            'favicon_url.max' => trans('setting.site_info.validation.favicon_url_max', ['max' => 500]),

            'meta_description.required' => trans('setting.site_info.validation.meta_description_required'),
            'meta_description.min' => trans('setting.site_info.validation.meta_description_min', ['min' => 10]),
            'meta_description.max' => trans('setting.site_info.validation.meta_description_max', ['max' => 255]),

            'prefix.required' => trans('setting.site_info.validation.prefix_required'),
            'prefix.*.required' => trans('setting.site_info.validation.prefix_each_required'),
            'prefix.*.min' => trans('setting.site_info.validation.prefix_each_min', ['min' => 1]),
            'prefix.*.max' => trans('setting.site_info.validation.prefix_each_max', ['max' => 4]),
        ];
    }
}
