<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'first_name' => 'required|string|min:3|max:100',
            'last_name' => 'required|min:3|max:100',
            'email' => 'required|string|email|max:100|unique:users',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        return response()->json($validator->errors(), 400);
    }
}
