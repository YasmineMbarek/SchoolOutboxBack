<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use function response;


class CreateRegionsRequest extends FormRequest
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
        $regionId = $this->route()->parameter('region_id');

        if (!$regionId) {
            $rules = ['required', 'string', 'max:20', 'unique:regions'];
        } else {
            $rules = ['required', 'string', 'max:20', 'unique:regions,name,' . $regionId];
        }

        return [
            'name' => $rules,
        ];
    }
}
