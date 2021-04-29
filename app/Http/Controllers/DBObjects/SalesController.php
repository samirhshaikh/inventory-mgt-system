<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\ReturnItemRequest;
use App\Services\SalesService;
use Illuminate\Http\JsonResponse;

class SalesController extends BaseController
{
    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $sales_service = new SalesService();
        try {
            $response = [];
            $response['record'] = $sales_service->getSingleSales($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param ReturnItemRequest $request
     * @return JsonResponse
     */
    public function returnItem(ReturnItemRequest $request): JsonResponse
    {
        $sales_service = new SalesService();

        try {
            $sales_service->returnItem($request);

            return $this->sendOK([], self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
