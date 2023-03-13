<?php

namespace App\Http\Requests\Customer\Profile;

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
            'region_id' => 'required|int',
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:customers',
            'password' => 'required|string|confirmed|min:6',
            'grade' => 'required||min:2|max:100',
            'image'=>'required',
            'image.*' => 'mimes:jpeg,jpg,png,'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return response()->json($validator->errors()->all(), 400);
    }
    public function messages()
    {
        return [
            'first_name.required' => "Please write your first name",
            'last_name.required' => "Please write your first name",
            "first_name.min" => "The first_name has to have at least :min characters.",
            "first_name.max" => "The first_name has to have no more than :max characters.",
            "last_name.min" => "The first_name has to have at least :min characters.",
            "last_name.max" => "The first_name has to have no more than :max characters.",
            "region_id.required" => "Please write a region",
        ];
    }
}
