<?php

namespace App\Http\Requests;

class LoginRequest extends FormRequest {
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
            'username' => 'required|string',
            'password' => 'required|string'
        ];
    }

    /**
     * Get the error messages for the defined validation rules
     *
     * @return array
     */
    public function messages() {
        return [
            'username.required' => 'Username cannot be empty',
            'password.required' => 'Password cannot be empty'
        ];
    }
}
?>
