<?php

namespace Agency\SiteVisit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('customer')->check();
    }

    public function rules(): array
    {
        return [
            'product_id'          => 'required|exists:products,id',
            'address'             => 'required|string|min:10',
            'preferred_date'      => 'required|date|after:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening',
            'notes'               => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'          => 'Please select a product for the site visit.',
            'product_id.exists'            => 'The selected product does not exist.',
            'address.required'             => 'Please provide the full visit address.',
            'address.min'                  => 'Address must be at least 10 characters.',
            'preferred_date.required'      => 'Please select a preferred date.',
            'preferred_date.after'         => 'Preferred date must be a future date.',
            'preferred_time_slot.required' => 'Please select a time slot.',
            'preferred_time_slot.in'       => 'Time slot must be morning, afternoon, or evening.',
        ];
    }
}
