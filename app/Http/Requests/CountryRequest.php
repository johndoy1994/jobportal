<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Country;
use App\MyAuth;

class CountryRequest extends Request
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
        // echo '<pre>';
        // print_r(get_class_methods($this));exit;
        $rules= [
            'name' => 'required|unique:countries'
        ];
        if($this->has('isedit')){
            //$rules['name'] = 'required|unique:countries,name,'.$this->Country->id;
            $rules['name'] = [
                'required',
                'unique:countries,name,'.$this->Country->id
            ];
        }

        return $rules;
   
    }

    public function messages() {
        $rules= [
            'name.required'=>'Country name must not be empty.'
        ];

        if($this->has('isedit')){
            $rules['name.required'] = 'Country name must not be empty.';
        }

        return $rules;
    }
}
