<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class BaseAPIController extends BaseController
{
    protected function sendResponse($message, $status, $data = [])
    {
        $reply = [
            'status_code' => $status,
            'message' => $message
        ];

        if (!empty($data)) {
            $reply['response'] = $data;
        }

        return response()->json($reply);
    }

    public function sendOK($data, $message = 'OK', $status = JsonResponse::HTTP_OK)
    {
        return $this->sendResponse($message, $status, $data);
    }

    public function sendError($data, $message = 'System Error', $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->sendResponse($message, $status, $data);
    }
}

?>
