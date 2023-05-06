<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityFilterRequest extends FormRequest
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
            'filter_type' => ['string', 'required_with:date,date_start,date_end', 'in:single,range'],
            'date' => ['nullable', 'date'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'after:date_start']
        ];
    }
}
