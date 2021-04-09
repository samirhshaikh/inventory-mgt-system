<?php
/**
 * This is the middleware for all the public pages except login.
 * It checks whether the user is logged in or not before giving access to the system
 */

namespace App\Http\Middleware;

use Closure;

class CheckForAuth {
    protected $except = ['/', 'index', 'login', 'doLogin'];

    /**
     * Handle an incoming request
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return $mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        if (!session('user', false)) {
            return redirect('login');
        }
        if (!session('api_token', false)) {
            return redirect('login');
        }

        return $next($request);
    }

    /**
     * Determine if the request should be ignored
     * 
     * @param \Illuminate\Http\Request
     * @return bool
     */
    protected function inExceptArray($request) {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
?>