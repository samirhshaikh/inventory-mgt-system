<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\DuplicateNameException;
use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveHandsetRequest;
use App\Services\HandsetsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HandsetsController extends BaseController
{
    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function changeActiveStatus(IdRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $handsets_service->changeActiveStatus($request);

            return $this->sendOK([], "status_changed");
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function getSingle(IdRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $response = [];
            $response["record"] = $handsets_service->getSingle($request);

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
     * @param SaveHandsetRequest $request
     * @return JsonResponse
     */
    public function save(SaveHandsetRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $id = $handsets_service->save($request);

            return $this->sendOK(
                [
                    "id" => $id,
                ],
                self::RECORD_SAVED
            );
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (DuplicateNameException $e) {
            return $this->sendError(
                self::DUPLICATE_NAME,
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $handsets_service->delete($request);

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
     * @param Request $request
     * @return JsonResponse
     */
    public function checkDuplicateName(Request $request): JsonResponse
    {
        $handsets_service = new HandsetsService();

        try {
            $handsets_service->checkDuplicateName($request);

            return $this->sendOK([]);
        } catch (DuplicateNameException $e) {
            return $this->sendError(
                self::DUPLICATE_NAME,
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
