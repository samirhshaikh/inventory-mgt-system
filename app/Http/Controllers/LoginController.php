<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class LoginController extends Controller
{
    /**
     * Check the user credentials by doing a request to api/generateToken
     *
     * @param LoginRequest $request
     * @return mixed
     */
    public function doLogin(LoginRequest $request)
    {
        $userController = new UserController();
        $loginResponse = $userController->login($request);

        $status = $loginResponse->status();

        switch ($status) {
            case JsonResponse::HTTP_OK:
                $loginResponse = json_decode($loginResponse->content(), true);

                session([
                    "user" => strtolower($request->get("username")),
                    "user_details" => Arr::get(
                        $loginResponse,
                        "response.user_details"
                    ),
                    "api_token" => Arr::get(
                        $loginResponse,
                        "response.access_token"
                    ),
                    "expires_at" => Arr::get(
                        $loginResponse,
                        "response.expires_at"
                    ),
                ]);

                $return = ["error" => "", "api_token" => session("api_token")];

                break;
            default:
                $loginResponse = json_decode($loginResponse->content(), true);

                $return = [
                    "error" => $this->formatErrorMessages(
                        Arr::get($loginResponse, "error")
                    ),
                ];

                break;
        }

        return response()
            ->json($return)
            ->setStatusCode($status);
    }

    /**
     * Logs out the user and clears the session
     */
    public function doLogout()
    {
        session()->flush();

        return redirect("login");
    }

    /**
     * @param $error
     * @return string
     */
    private function formatErrorMessages($error): string
    {
        $error_codes_to_messages = [
            "invalid_credentials" => "Invalid Username/Password",
            "could_not_create_token" => "Could not create the token",
        ];

        return array_key_exists($error, $error_codes_to_messages)
            ? $error_codes_to_messages[$error]
            : "Unknown Error";
    }
}
