<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use function response;


class CreateCategoriesRequest extends FormRequest
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
        $categoryId = $this->route()->parameter('category_id');

        if (!$categoryId) {
            $rules = ['required', 'string', 'max:20', 'unique:categories'];
        } else {
            $rules = ['required', 'string', 'max:20', 'unique:categories,type,' . $categoryId];
        }

        return [
            'type' => $rules,
        ];
    }

}
