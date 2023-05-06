<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class ManageSaleRequest extends FormRequest
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
                    'title' => ['string', 'max:100', 'min:1'],
                    'customer_id' => ['required', 'integer', 'exists:customers,id'],
                    'sale_data' => [
                        'required', 
                        'json',
                        function($attribute, $value, $fail) {
                            $json = json_decode($value, true);
        
                            //### When invalid JSON string.
                            if (is_null($json)) return;
        
                            //### When empty array like [].
                            if (count($json) == 0) return $fail('Invalid format or invalid values.');
        
                            $this->validateSaleData($json, $fail);
                        }
                    ],
                ];
            case 'update':
                return [
                    'sale_data' => [
                        'required', 
                        'json',
                        function($attribute, $value, $fail) {
                            $json = json_decode($value, true);
        
                            //### When invalid JSON string.
                            if (is_null($json)) return;
        
                            //### When empty array like [].
                            if (count($json) == 0) return $fail('Invalid format or invalid values.');
        
                            $this->validateSaleData($json, $fail);
                        }
                    ],
                ];
            default:
                return [];
        }
    }

    //### for modify 'sale_data' into array after validation
    protected function passedValidation()
    {
        $sale_data = json_decode($this->input('sale_data'), true);

        //### resolve double
        foreach ($sale_data as $i => $item) {
            $sale_data[$i]['discount'] = $this->resolveDouble($item['discount']);
        }

        $this->merge([
            'sale_data' => $sale_data,
        ]);
    }

    private function validateSaleData($json, $fail) {
        $error_qty_not_available = false;

        $rules = [
            '*.product_id' => 'required|integer|distinct|exists:products,id',
            '*.qty' => [
                'required',
                'integer',
                'min:1',
                //### Check 'qty' s are available.
                function ($attribute, $value, $fail) use ($json, &$error_qty_not_available) {
                    $index = explode('.', $attribute)[0]; // Because value format of $attribute is like '0.qty' 0 is a index

                    if (!isset($json[$index]['product_id'])) return;

                    $product_id = $json[$index]['product_id'];
                    
                    $availableqty = Product::getAvailableQty($product_id);

                    //### if the given 'product_id' not exists, value of '$availableqty' is null.
                    if (is_null($availableqty)) return;
                    
                    if ($availableqty < $value) {
                        $error_qty_not_available = true;
                        $fail('Available quantity of product #'.$product_id.' is only '.$availableqty);
                    } 
                },
            ],
            '*.discount' => 'required|numeric|min:0',
        ];
        
        $validator = Validator::make($json, $rules);

        if ($validator->fails()) {
            if ($error_qty_not_available) {
                $fail('Given quantity of some product(s) is not available.');
            } else {
                $fail('Invalid format or invalid values.');
            }
        }
    }

    private function resolveDouble($val) {
        return (number_format((float)$val, 2, '.', ''));
    }
}
