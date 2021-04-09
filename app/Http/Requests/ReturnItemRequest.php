<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules which apply to this request
     *
     * @return array
     */
    public function rules() {
        return [
            'IMEI' => 'required|string',
            'ReturnDate' => 'required|string'
        ];
    }

    /**
     * Get the error messages for the defined validation rules
     *
     * @return array
     */
    public function messages() {
        return [
            'IMEI.required' => 'IMEI cannot be empty',
            'ReturnDate.required' => 'Return Date cannot be empty'
        ];
    }
}
