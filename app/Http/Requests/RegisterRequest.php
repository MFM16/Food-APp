<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
            'verifyPassword' => 'required|min:8|same:password'
        ];
    }

    public $validator = null;
    protected function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tidak boleh kosong !',
            'email.required' => 'Email tidak boleh kosong !',
            'email.unique' => 'Email sudah digunakan !',
            'password.required' => 'Kata sandi tidak boleh kosong !',
            'password.min' => 'Minimal kata sandi 8 karakter !',
            'verifyPassword.required' => 'Ulangi kata sandi tidak boleh kosong !',
            'verifyPassword.min' => 'Minimal ulangi kata sandi 8 karakter !',
            'verifyPassword.same' => 'Kata sandi belum sesuai !',
        ];
    }
}
