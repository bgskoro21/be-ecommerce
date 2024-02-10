<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "address_id" => ['numeric'], 
            "first_name" => ['max:100'],
            "last_name" => ['max:100'],
            "address" => ['max:255'],
            "subdistrict" => ['max:100'],
            "city" => ['max:100'],
            "province" => ['max:100'],
            "zip_code" => ['max:100'],
            "no_hp" => ['max:15'],
            "total_amount" => ['required', 'numeric'],
            "order_items" => ['required', 'array'],
            "order_items.*" => ['required', 'numeric']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->getMessageBag()
        ], 400));
    }
}
