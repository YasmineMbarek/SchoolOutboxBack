<?php

namespace App\Http\Requests\Customer\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array
     */
    public function rules()
    {
        return [
            'region_id' => 'required|int',
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:customers',
            'password' => 'required|string|confirmed|min:6',
            'grade' => 'required|string',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        return response()->json($validator->errors(), 422);
    }
}
