<?php

namespace App\Http\Requests;

class ReturnItemRequest extends FormRequest
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
            'InvoiceId' => 'required|numeric',
            'IMEI' => 'required|string',
            'ReturnedDate' => 'required|string'
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
            'InvoiceId.required' => 'InvoiceId is required',
            'IMEI.required' => 'IMEI cannot be empty',
            'ReturnedDate.required' => 'Returned Date cannot be empty'
        ];
    }
}
