<?php

namespace App\Http\Middleware;

use App\Models\AppSettings;
use Closure;

class GetAppSettings
{
    protected $except = [
        'api/auth'
    ];

    /**
     * Handle an incoming request
     * 
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return $mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //Make sure the user is logged in
        if (session('api_token', false) === false) {
            return $next($request);
        }

        //If we have already got app_settings in session then no need to get again
        if (session('app_settings', false) !== false) {
            return $next($request);
        }

        //Get the app settings
        $settings = AppSettings::where('UserName', session('user'))
            ->get();

        $payload = [];
        foreach ($settings as $setting) {
            $payload[$setting->State] = json_decode($setting->Payload, true);
        }

        //Initialise with empty value
        if (!array_key_exists('framework', $payload)) {
            $payload['framework'] = [];
        }

        //Initialise with empty value
        if (!array_key_exists('store_settings', $payload)) {
            $payload['store_settings'] = [];
        }

        //Initialise with empty value
        if (!array_key_exists('datatable', $payload)) {
            $payload['datatable'] = [];
        }

        session(['app_settings' => $payload]);

        return $next($request);
    }
}
