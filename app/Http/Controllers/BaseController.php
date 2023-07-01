<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    const RECORD_SAVED = "record_saved";
    const RECORD_DELETED = "record_deleted";

    const RECORD_NO_FOUND = "record_not_found";
    const DUPLICATE_NAME = "duplicate_name";
    const DUPLICATE_IMEI = "duplicate_imei";
    const INVALID_DATA = "invalid_data";
    const RECORD_REFERENCE_FOUND = "record_reference_found";

    /**
     * @param $message
     * @param $status
     * @param array $data
     * @return JsonResponse
     */
    protected function sendResponse($message, $status, $data = []): JsonResponse
    {
        $reply = [
            "message" => $message,
        ];

        if (!empty($data)) {
            $reply["response"] = $data;
        }

        return response()
            ->json($reply)
            ->setStatusCode($status);
    }

    /**
     * @param $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    public function sendOK(
        $data,
        $message = "OK",
        $status = JsonResponse::HTTP_OK
    ): JsonResponse {
        return $this->sendResponse($message, $status, $data);
    }

    /**
     * @param string $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    public function sendError(
        $message = "System Error",
        $data = [],
        $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return $this->sendResponse($message, $status, $data);
    }
}

?>
