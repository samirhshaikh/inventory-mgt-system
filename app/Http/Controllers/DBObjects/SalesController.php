<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\InvalidDataException;
use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\ReturnItemRequest;
use App\Http\Requests\SaveSaleRequest;
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
     * @param SaveSaleRequest $request
     * @return JsonResponse
     */
    public function save(SaveSaleRequest $request): JsonResponse
    {
        $sales_service = new SalesService();

        try {
            $records_count = $sales_service->save($request);
            //start here. Need to see whether the record is correctly saved or not

            return $this->sendOK(['records_count' => $records_count], self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [],JsonResponse::HTTP_NOT_FOUND);
        } catch (InvalidDataException $e) {
            return $this->sendError(self::INVALID_DATA, [], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $sales_service = new SalesService();

        try {
            $sales_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
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

    public function getNextInvoiceNo()
    {
        $sales_service = new SalesService();

        return $sales_service->getNextInvoiceNo();
    }
}
