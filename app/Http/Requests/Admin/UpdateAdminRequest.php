<?php

namespace App\Http\Requests\admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class UpdateAdminRequest extends FormRequest
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
        $adminRoleId = Role::query()->firstWhere('name', '=', Role::ROLE_ADMIN)->id;

        return [
            'region_id' => 'int|required_if:role_id,==,' . $adminRoleId,
            'role_id' => 'required|int',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return response()->json($validator->errors(), 400);
    }
}
