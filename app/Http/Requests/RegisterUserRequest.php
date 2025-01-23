<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterUserRequest extends FormRequest
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

            "name" => "required|min:3|max:30|regex:/^[\pL\s]+$/u|unique:users,name",
            "email" => "required|email|unique:users,email",
            "password" => [
                "required",
                "min:8",
                "regex:/[a-z]/",
                "regex:/[A-Z]/",
                "regex:/[0-9]/"
            ],
            "confirm_password" => "same:password"

        ];
    }

    public function messages()
    {
        return [

            "name.required" => "Név mező kitöltése kötelező!",
            "name.min" => "Minimum 3 karakter!",
            "name.max" => "Maximum 30 karakter!",
            "name.regex" => "A név csak betűket tartalmazhat!",
            "name.unique" => "Hibás felhasználónév!",
            "email.required" => "Email cím megadása kötelező!",
            "email.email" => "Nem valós email cím!",
            "email.unique" => "Hibás email cím!",
            "password.required" => "Adjon meg egy jelszót!",
            "password.min" => "Túl rövid jelszó, minimum 8 karakter!",
            "password.regex" => "A jelszónak tartalmaznia kell kis- és nagybetűt és számot!",
            "confirm_password.required" => "Kérjük, erősítse meg a jelszavát!",
            "confirm_password.same" => "Nem egyezik a két jelszó!"

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => "Adatbeviteli hiba!",
            "error" => $validator->errors(),
        ], 422));
    }
}
