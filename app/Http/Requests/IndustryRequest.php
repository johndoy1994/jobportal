<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\MyAuth;

class IndustryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return MyAuth::check("admin");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    
    public function rules()
    {
        $rules = [
            'name' => 'required'
        ];
        return $rules;
    }

    public function messages() {
        $rules = [
            'name.required'=>'Industry name must not be empty.'
        ];

        return $rules;
    }
}
