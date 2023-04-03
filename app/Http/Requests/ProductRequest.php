<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required',
            'price' => 'required|min:1|numeric',
            'stock' => 'numeric',
            'category_id' => 'required',
        ];
    }

    /**
     * Mensajes que regresa la validación.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'El nombre del producto es requerido',
            'price.required' => 'El precio',
            'price.min' => 'El precio minimo del producto debe ser 1',
            'price.numeric' => 'El precio debe contener un valor numerico',
            'stock' => 'El stock debe contener un valor numerico',
            'category_id' => 'La categoria del producto es requerida',
        ];
    }
}
