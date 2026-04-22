<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;

class RegisterFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'min:10'],
            'address' => ['nullable', 'string', 'min:5'],
            'user_type' => ['required', 'in:guest,owner,cleaner,inspector,receptionist'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.min' => 'Name must be at least 3 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'address.min' => 'Address must be at least 5 characters.',
            'user_type.required' => 'User type is required.',
            'user_type.in' => 'Invalid user type selected.',
        ];
    }
}
