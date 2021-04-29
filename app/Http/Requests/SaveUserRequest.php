<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveUserRequest extends FormRequest
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
            'UserName' => 'required|string|max:20',
            'Password' => 'required|string|max:20'
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
            'UserName.required' => 'Username is required',
            'Password.required' => 'Password is required',
            'UserName.max' => 'Username cannot be more than 20 characters long',
            'Password.max' => 'Password cannot be more than 20 characters long'
        ];
    }
}
