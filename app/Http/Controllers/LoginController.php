<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * Check the user credentials by doing a request to api/generateToken
     * 
     * @param App\Http\Requests\LoginRequest $request
     * @return $mixed
     */
    public function doLogin(LoginRequest $request)
    {
        $userController = new UserController;
        $loginResponse = $userController->login($request);

        $error = '';

        $return = [];

        switch ($loginResponse->status()) {
            case 200:
                $loginResponse = json_decode($loginResponse->content(), true);

                session([
                    'user' => $request->get('username'),
                    'user_details' => array_get($loginResponse, 'response.user_details'),
                    'api_token' => array_get($loginResponse, 'response.access_token'),
                    'expires_at' => array_get($loginResponse, 'response.expires_at')
                ]);

                $return = ['error' => '', 'api_token' => session('api_token')];

                break;
            default:
                $loginResponse = json_decode($loginResponse->content(), true);
                $error = $this->formatErrorMessages(array_get($loginResponse, 'error'));

                $return = ['error' => $error];

                break;
        }

        return response()->json($return);
    }

    /**
     * Logs out the user and clears the session
     */
    public function doLogout() {
        session()->flush();
        
        return redirect('login');
    }

    private function formatErrorMessages($error)
    {
        $error_codes_to_messages = [
            'invalid_credentials' => 'Invalid Username/Password',
            'could_not_create_token' => 'Could not create the token'
        ];

        return array_key_exists($error, $error_codes_to_messages) ? $error_codes_to_messages[$error] : 'Unknown Error';
    }
}
