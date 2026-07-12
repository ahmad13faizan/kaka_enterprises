<?php

namespace Agency\ProductSample\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SampleRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('customer')->check();
    }

    public function rules(): array
    {
        return [
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'nullable|integer|min:1|max:5',
            'shipping_address' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'       => 'Please select a product.',
            'product_id.exists'         => 'The selected product does not exist.',
            'shipping_address.required' => 'Please provide a shipping address.',
            'shipping_address.min'      => 'Address must be at least 10 characters.',
        ];
    }
}
