<?php

namespace App\Http\Requests;

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
            'InvoiceId' => 'required|numeric',
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
            'InvoiceId.required' => 'InvoiceId cannot be empty',
            'IMEI.required' => 'IMEI cannot be empty',
            'ReturnDate.required' => 'Return Date cannot be empty'
        ];
    }
}
