<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => ['required', 'max:100'],
            "description" => ['required'],
            "price" => ['required', 'numeric'],
            "category_id" => ['required', 'numeric'],
            "product_image" => ['required','image','mimes:jpeg,png,jpg,gif,svg','max:2048'] 
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->getMessageBag()
        ]), 400);
    }
}
