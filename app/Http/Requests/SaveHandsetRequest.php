<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveHandsetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'Name' => 'required',
            'ColorId' => 'required',
            'MakeId' => 'required',
            'ModelId' => 'required',
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
            'Name.required' => 'Name is required',
            'ColorId.required' => 'Color is required',
            'MakeId.required' => 'Manufacturer is required',
            'ModelId.required' => 'Model is required'
        ];
    }
}
