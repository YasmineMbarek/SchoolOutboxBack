<?php

namespace App\Http\Requests\Customer\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email|max:100|unique:customers',
            'old_password' => 'required|string|confirmed|min:6',
            'password' => 'required|string|confirmed|min:6',
            'confirm_password' => ['same:password'],
        ];
    }
    public function failedValidation(Validator $validator)
    {
        return response()->json($validator->errors(), 400);
    }
}
