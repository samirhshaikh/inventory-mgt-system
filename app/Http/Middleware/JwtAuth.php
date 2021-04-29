<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //grab the token
        $token = TYJWT::getToken();
        if ($token === null) {
            return response()
                ->json([
                    'message' => 'Invalid token supplied.'
                ])
                ->setStatusCode(JsonResponse::HTTP_UNAUTHORIZED);
        }

        //parse it
        if (($payload = TYJWT::check(true)) === false) {
            return response()
                ->json([
                    'message' => 'Invalid token supplied.'
                ])
                ->setStatusCode(JsonResponse::HTTP_UNAUTHORIZED);
        }

        //grab the user the token is on about
        $user = User::where('Username', $payload->get('email'))
            ->where('Password', $payload->get('password'))
            ->firstOrFail();

        if ($user) {
            auth('api')->setUser($user);

            return $next($request);
        }
    }
}
