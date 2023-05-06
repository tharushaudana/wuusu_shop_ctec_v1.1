<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageSupplierRequest extends FormRequest
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
                    'name' => ['required', 'string', 'max:255'],
                ];
            case 'update':
                return [
                    'name' => ['string', 'max:255'],
                ];
            default:
                return [];
        }
    }
}
