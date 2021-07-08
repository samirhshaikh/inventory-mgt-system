<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], JsonResponse::HTTP_UNAUTHORIZED);
        } else {
            return route('login');
        }
    }
}
