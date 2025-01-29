<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
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
            'email' => 'required|email|exists:admins,email',
            'password' => 'required|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Az email cím megadása kötelező!',
            'email.email' => 'Nem valós email cím!',
            'email.exists' => 'Nincs ilyen admin ezzel az email címmel!',
            'password.required' => 'A jelszó megadása kötelező!',
            'password.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie!',
        ];
    }
}
