<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        switch ($this->route()->getActionMethod()) {
            case 'store':
                return [
                    'name' => ['required', 'string', 'max:100'],
                    'address' => ['string', 'nullable'],
                    'phone' => ['string', 'unique:customers,phone', 'nullable',],
                    'email' => ['email', 'unique:customers,email', 'nullable'],
                ];
            case 'update':
                return [
                    'name' => ['string', 'max:100'],
                    'address' => ['string', 'nullable'],
                    'phone' => ['string', 'unique:customers,phone', 'nullable',],
                    'email' => ['email', 'unique:customers,email', 'nullable'],
                ];
            default:
                return [];
        }
    }

    public function withValidator($validator)
    {
        //### Skip when action is not equal to 'store'
        if ($this->route()->getActionMethod() != 'store') return;

        //### Add addtional rules after validated.
        $validator->after(function ($validator) {
            $data = $this->validated();

            if (empty($data['email']) && empty($data['phone'])) {
                $validator->errors()->add('email_or_phone', 'email or phone number is required.');
            }
        });
    }
}
