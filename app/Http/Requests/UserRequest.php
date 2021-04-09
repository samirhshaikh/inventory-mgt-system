<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {
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
            'username' => 'required|string|max:20',
            'password' => 'required|string|max:20'
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
            'password.required' => 'Password cannot be empty',
            'username.max' => 'Username cannot be more than 20 characters long',
            'password.max' => 'Password cannot be more than 20 characters long'
        ];
    }
}
?>