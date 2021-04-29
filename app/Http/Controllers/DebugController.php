<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class DebugController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function getDebugInfo(): JsonResponse
    {
        return $this->sendOK([
            'session' => session()->all()
        ]);
    }
}
