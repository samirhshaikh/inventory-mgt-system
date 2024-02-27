<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveSaleRequest extends FormRequest
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
            "children" => "required|array|min:1",
            "CustomerId" => "required",
            "InvoiceDate" => "required",
            "PaymentMethod" => "required",
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
            "children.required" => "Items are required",
            "children.min" => "Atleast one item is required",
            "CustomerId.required" => "Customer id is required",
            "InvoiceDate.required" => "Invoice Date is required",
            "PaymentMethod.required" => "Payment Method is required",
        ];
    }
}
