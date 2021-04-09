<?php

namespace App\Http\Controllers;

class DebugController extends BaseController {
    /**
     * Get all the debug info
     */
    public function getDebugInfo() {
        return $this->sendOK([
            'session' => session()->all()
        ]);
    }
}