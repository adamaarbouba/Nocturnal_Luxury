<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->slug === 'owner';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255|unique:hotels,name|unique:hotel_requests,name,NULL,id,owner_id,' . auth()->id(),
            'description' => 'nullable|string|max:2000',
            'phone' => 'required|string|min:10|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|min:5|max:500',
            'city' => 'required|string|min:2|max:100',
            'country' => 'required|string|min:2|max:100',

        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Hotel name is required.',
            'name.unique' => 'A hotel with this name already exists or you have a pending request.',
            'phone.required' => 'Phone number is required.',
            'email.required' => 'Email address is required.',
            'address.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'country.required' => 'Country is required.',

        ];
    }
}
