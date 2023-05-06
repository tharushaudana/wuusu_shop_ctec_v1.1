<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManageProductRequest extends FormRequest
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
                    'itemcode' => ['required', 'string', 'max:255', 'unique:products,itemcode'],
                    'description' => ['required', 'string'],
                    'unit' => ['required', 'string', 'max:255'],
                    'minqty' => ['required', 'integer', 'min:0'],
                    'max_retail_price' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'saler_discount_rate' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'profit_percent' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'price_matara' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'price_akuressa' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                ];
            case 'update':
                $product = $this->route('_product'); //### Current Product

                return [
                    'itemcode' => ['string', 'max:255', 'unique:products,itemcode,'.$product->id],
                    'description' => ['string'],
                    'unit' => ['string', 'max:255'],
                    'minqty' => ['integer', 'min:0'],
                    'max_retail_price' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'saler_discount_rate' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'profit_percent' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'price_matara' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                    'price_akuressa' => ['regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
                ];
            default:
                return [];
        }
    }
}
