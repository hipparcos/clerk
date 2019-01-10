<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class Register extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.attributes.name' => ['required', 'string', 'max:255'],
            'data.attributes.email' => [
                'required', 'string', 'email', 'max:320', 'unique:users,email'
            ],
            'data.attributes.password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    /**
    * Get custom attributes for validator errors.
    *
    * @return array
    */
    public function attributes()
    {
        return [
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.password' => 'password',
        ];
    }
}
