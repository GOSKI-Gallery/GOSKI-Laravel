<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|unique:users,username|string|max:255',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ];
    }
}
