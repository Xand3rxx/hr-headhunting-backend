<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class Registration extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->uncompromised()],
        ];
    }

    public function messages()
    {
        return [
            'email.required'            => 'Please provide valid email.',
            'password.required'         => 'Please provide a password.',
            'password.string'           => 'The password must be a string.',
            'password.min'              => 'The password must be at least 8 characters long.',
            'password.mixed_case'       => 'The password must contain both upper and lower case letters.',
            'password.uncompromised'    => 'The password has been compromised and is not secure. Please choose a different password.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message'   => 'Failed to process registration request due to validation errors.',
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
