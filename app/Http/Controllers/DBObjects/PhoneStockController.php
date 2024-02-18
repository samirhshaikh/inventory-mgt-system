<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\DuplicateIMEIException;
use App\Exceptions\InvalidIMEIException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\IMEIRequest;
use App\Services\PhoneStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhoneStockController extends BaseController
{
    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $phonestock_service = new PhoneStockService();

        try {
            $response = [];
            $response["record"] = $phonestock_service->getSingle($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $phonestock_service = new PhoneStockService();

        try {
            $phonestock_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (ReferenceException $e) {
            return $this->sendError(
                self::RECORD_REFERENCE_FOUND,
                [],
                JsonResponse::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * @param IMEIRequest $request
     * @return JsonResponse
     */
    public function validateIMEI(IMEIRequest $request): JsonResponse
    {
        $phonestock_service = new PhoneStockService();

        try {
            $phonestock_service->checkDuplicateIMEI($request);
        } catch (DuplicateIMEIException $e) {
            return $this->sendError(
                self::DUPLICATE_IMEI,
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $phonestock_service->validateIMEI($request);
        } catch (InvalidIMEIException $e) {
            return $this->sendError(
                self::INVALID_IMEI,
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->sendOK([]);
    }
}
