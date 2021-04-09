<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class BaseAPIController extends BaseController {
    protected function sendResponse($message, $status, $data = []) {
        $reply = [
            'status_code' => $status,
            'message' => $message
        ];

        if (!empty($data)) {
            $reply['response'] = $data;
        }

        return response()->json($reply);
    }

    public function sendOK($data, $message = 'OK', $status = 200) {
        return $this->sendResponse($message, $status, $data);
    }

    public function sendError($data, $message = 'System Error', $status = 500) {
        return $this->sendResponse($message, $status, $data);
    }

    public function canSendVerbose(Request $request = null) {
        if (is_null($request)) {
            $request = request();
        }

        if (!$request->get('verbose', false)) {
            return false;
        }
        
        return true;
    }
}
?>