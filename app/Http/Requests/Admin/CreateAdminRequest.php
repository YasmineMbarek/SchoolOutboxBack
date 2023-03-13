<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class CreateAdminRequest extends FormRequest
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
       $adminId = $this->route()->parameter('admin_id');

        if (!$adminId) {
            $rules = ['required', 'string', 'max:100', 'unique:users'];
        } else {
            $rules = ['required', 'string', 'max:100', 'unique:users,email,' . $adminId];
        }

        return [
            'email' => $rules,
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
        ];
    }
}
