<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreSchoolRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_name' => [
                'required',
                'string',
                'min:3',
                'max:150',
            ],

            'registration_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('schools', 'registration_number'),
            ],

            'school_type' => [
                'required',
                Rule::in([
                    'primary',
                    'secondary',
                    'college',
                    'university',
                    'training_institution',
                    'other',
                ]),
            ],

            'county' => [
                'nullable',
                'string',
                'max:100',
            ],

            'town' => [
                'nullable',
                'string',
                'max:100',
            ],

            'school_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'school_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'name' => [
                'required',
                'string',
                'min:2',
                'max:150',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'phone'),
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],

            'terms' => [
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'school_name.required' => 'Please enter the school or institution name.',
            'email.unique' => 'An account already exists with this email address.',
            'phone.unique' => 'An account already exists with this phone number.',
            'registration_number.unique' => 'This institution registration number is already registered.',
            'terms.accepted' => 'You must accept the terms and privacy policy.',
        ];
    }
}