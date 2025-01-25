<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => [
                "nullable",
                "min:8",
                "regex:/[a-z]/",
                "regex:/[A-Z]/",
                "regex:/[0-9]/",
                "confirmed"
            ],
            'country' => 'nullable|required|string|max:30',
            'postal_code' => 'nullable|required|string|max:10',
            'city' => 'nullable|required|string|max:40',
            'street' => 'nullable|required|string|max:80',
            'address_line_2' => 'nullable|string|max:80'
        ];
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'A jelszavak nem egyeznek!',
            'password.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie!',
            'country.required' => 'Ország mező kitöltése kötelező!',
            'country.max' => 'Ország mezőben maximum 30 karakter lehet!',
            'postal_code.required' => 'Irányítószám mező kitöltése kötelező!',
            'postal_code.max' => 'Irányítószám mezőben maximum 10 karakter lehet!',
            'city.required' => 'Város mező kitöltése kötelező!',
            'city.max' => 'Város mezőben maximum 40 karakter lehet!',
            'street.required' => 'Utca, házszám mező kitöltése kötelező!',
            'street.max' => 'Utca, házszám mezőben maximum 80 karakter lehet!',
            'address_line_2.max' => 'Egyéb címadatok mezőben maximum 80 lehet!'
        ];
    }
}
