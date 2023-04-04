<?php

namespace App\Http\Requests;

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
        $rules = [
            'fullname'=> 'required',
            'email'=> 'required|email',
            'password'=> 'required|min:6'
        ];

        // Si es diferente a Post
        if($this->method() !== 'PUT')
        {
            $rules ['email' ] = 'required|email|unique:users,email,' . $this->id;

        }

        return $rules;    ;
    }

    /**
     * Messages to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'fullname.required' => 'El nombre completo es requerido',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email no tiene el formato correcto',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'El minimo de carácteres para la contraseña es 6',
        ];
    }
}
