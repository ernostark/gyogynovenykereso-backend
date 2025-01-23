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
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->user()->id,
            'password' => [
                "nullable",
                "required",
                "min:8",
                "regex:/[a-z]/",
                "regex:/[A-Z]/",
                "regex:/[0-9]/",
                "confirmed"
            ],
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Hibás email cím!',
            'password.confirmed' => 'A jelszavak nem egyeznek!',
            'password.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie!',
        ];
    }
}
