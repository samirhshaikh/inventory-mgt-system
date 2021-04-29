<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IMEIRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules which apply to this request
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'IMEI' => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'IMEI.required' => 'IMEI is required'
        ];
    }
}
