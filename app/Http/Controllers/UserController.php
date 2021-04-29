<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Http\Controllers\BaseAPIController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\PayloadFactory;
use Tymon\JWTAuth\JWTManager as JWT;
use Carbon\Carbon;

class UserController extends BaseAPIController
{
    /**
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function register(UserRequest $request): JsonResponse
    {
        $user = User::create([
            'UserName' => $request->get('username'),
            'Password' => $request->get('password')
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), JsonResponse::HTTP_CREATED);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['username', 'password']);

        try {
            $token = auth('api')->attempt($credentials);

            if (!$token) {
                return response()->json(['error' => 'invalid_credentials'], JsonResponse::HTTP_BAD_REQUEST);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = auth('api')->user();

        $payload = JWTFactory::iss('core')
            ->aud(null)
            ->iat(time())
            ->nbf(time())
            ->exp(60 * 60)
            ->sub($user->UserName)
            ->pdt('core')
            ->make();

        $token = JWTAuth::encode($payload);

        $ttl = auth('api')->factory()->getTTL();

        //Get the user details
        $user = User::where('UserName', $request->get('username'))
            ->get();

        return $this->sendOK([
            'access_token' => $token->get(),
            'expires_at' => $ttl * 60,
            'user_details' => $user->map->transform()->first()
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getAuthenticatedUser(): JsonResponse
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }
}
