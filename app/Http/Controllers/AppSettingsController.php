<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use Illuminate\Http\Request;

class AppSettingsController extends BaseController
{
    /**
     * Store the app_settings
     */
    public function storeAppSettings(Request $request)
    {
        $settings = $request->all();

        foreach ($settings as $state => $payload) {
            AppSettings::updateOrCreate(
                [
                    'UserName' => session('user'),
                    'State' => $state
                ],
                [
                    'Payload' => json_encode($payload),
                    'CreatedBy' => session('user')
                ]
            );
        }

        session(['app_settings' => $request->all()]);

        return $request;
    }
}
