<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveCustomerSalesRequest;
use App\Services\CustomerSalesService;
use Illuminate\Http\JsonResponse;

class CustomerSalesController extends BaseController
{
    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function changeActiveStatus(IdRequest $request): JsonResponse
    {
        $customers_service = new CustomerSalesService();

        try {
            $customers_service->changeActiveStatus($request);

            return $this->sendOK([], 'status_changed');
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $customers_service = new CustomerSalesService();

        try {
            $response = [];
            $response['record'] = $customers_service->getSingle($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param SaveCustomerSalesRequest $request
     * @return JsonResponse
     */
    public function save(SaveCustomerSalesRequest $request): JsonResponse
    {
        $customers_service = new CustomerSalesService();

        try {
            $id = $customers_service->save($request);

            return $this->sendOK([
                'id' => $id
            ], self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $customers_service = new CustomerSalesService();

        try {
            $customers_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        } catch (ReferenceException $e) {
            return $this->sendError(self::RECORD_REFERENCE_FOUND, [], JsonResponse::HTTP_FORBIDDEN);
        }
    }
}