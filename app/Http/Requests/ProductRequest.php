<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'photos' => 'array|min:1|max:5',
            'photos.*' => 'image|mimes:jpeg,jpg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'photos.max' => 'The total size of all photos must not exceed 2MB.',
        ];
    }
}
