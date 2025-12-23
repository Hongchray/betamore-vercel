<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'image'         => ['nullable', 'string', 'max:255'],
            'shipping_fee'  => ['required', 'numeric', 'max:255'],
            'is_active'     => ['boolean'],
            'description'   => ['nullable', 'string'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required'         => __('delivery.validation.name_required'),
            'name.string'           => __('delivery.validation.name_string'),
            'name.max'              => __('delivery.validation.name_max'),

            'image.string'          => __('delivery.validation.image_string'),
            'image.max'             => __('delivery.validation.image_max'),

            'shipping_fee.required' => __('delivery.validation.shipping_fee_required'),
            'shipping_fee.string'   => __('delivery.validation.shipping_fee_string'),
            'shipping_fee.max'      => __('delivery.validation.shipping_fee_max'),

            'description.string'    => __('delivery.validation.description_string'),
        ];
    }
}
