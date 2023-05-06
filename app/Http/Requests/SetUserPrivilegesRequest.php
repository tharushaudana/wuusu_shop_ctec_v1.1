<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetUserPrivilegesRequest extends FormRequest
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
        return [
            /*'privileges' => [
                'required', 
                'json',
                function($attribute, $value, $fail) {
                    $json = json_decode($value, true);

                    if (is_null($json)) return;
                    
                    $json = array_unique($json);

                    $is_fail = false;

                    foreach ($json as $id) {
                        if (!$this->checkIsValidPrivilegeId($id)) {
                            $is_fail = true;
                            break;
                        }
                    }

                    if ($is_fail) $fail('Some privilege ids are invalid!');
                }
            ]*/
            'privileges' => [
                'required', 
                'array',
                function($attribute, $value, $fail) {
                    $is_fail = false;

                    foreach ($value as $id) {
                        if (!$this->checkIsValidPrivilegeId($id)) {
                            $is_fail = true;
                            break;
                        }
                    }

                    if ($is_fail) $fail('Some privilege ids are invalid!');
                }
            ]
        ];
    }

    //### for modify 'privileges' into array after validation
    protected function passedValidation()
    {

        /*$this->merge([
            'privileges' => json_decode($this->input('privileges'), true),
        ]);*/
    }

    private function checkIsValidPrivilegeId($id) {
        $exists = false;

        foreach(config('userprivis') as $title => $privileges) {
            foreach($privileges as $name => $priviid) {
                if ($id == $priviid) {
                    $exists = true;
                    break;
                }
            }
        }

        return $exists;
    }
}
