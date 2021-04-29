<?php

namespace App\Http\Requests;

class UserRequest extends FormRequest
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
            'username' => 'required|string|max:20',
            'password' => 'required|string|max:20'
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
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
            'username.max' => 'Username cannot be more than 20 characters long',
            'password.max' => 'Password cannot be more than 20 characters long'
        ];
    }
}

?>
