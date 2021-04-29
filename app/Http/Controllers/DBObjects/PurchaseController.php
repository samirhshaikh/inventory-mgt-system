<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\DuplicateIMEIException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SavePurchaseRequest;
use App\Http\Requests\IdRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends BaseController
{
    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $purchase_service = new PurchaseService();

        try {
            $response = [];
            $response['record'] = $purchase_service->getSinglePurchase($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [],JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param SavePurchaseRequest $request
     * @return JsonResponse
     */
    public function save(SavePurchaseRequest $request): JsonResponse
    {
        $purchase_service = new PurchaseService();

        try {
            $records_count = $purchase_service->save($request);

            return $this->sendOK(['records_count' => $records_count], self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [],JsonResponse::HTTP_NOT_FOUND);
        } catch (InvalidDataException $e) {
            return $this->sendError(self::INVALID_DATA, [], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ReferenceException $e) {
            return $this->sendError(self::RECORD_REFERENCE_FOUND, [], JsonResponse::HTTP_FORBIDDEN);
        } catch (DuplicateIMEIException $e) {
            return $this->sendError(self::DUPLICATE_IMEI, [], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $purchase_service = new PurchaseService();

        try {
            $purchase_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        } catch (ReferenceException $e) {
            return $this->sendError(self::RECORD_REFERENCE_FOUND, [], JsonResponse::HTTP_FORBIDDEN);
        }
    }
}
