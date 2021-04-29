<?php

namespace App\Http\Controllers\DBObjects;

use App\Exceptions\DuplicateNameException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveObjectTypeNameRequest;
use App\Services\ObjectTypeNameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ObjectTypeNameController extends BaseController
{
    abstract protected function getModel();

    abstract protected function getRecordName();

    abstract protected function getColumnIdInReferenceTables();

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function changeActiveStatus(IdRequest $request): JsonResponse
    {
        $object_type_name_service = new ObjectTypeNameService($this->getModel(), $this->getColumnIdInReferenceTables());

        try {
            $object_type_name_service->changeActiveStatus($request);

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
        $object_type_name_service = new ObjectTypeNameService($this->getModel(), $this->getColumnIdInReferenceTables());

        try {
            $response = [];
            $response['record'] = $object_type_name_service->getSingle($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param SaveObjectTypeNameRequest $request
     * @return JsonResponse
     */
    public function save(SaveObjectTypeNameRequest $request): JsonResponse
    {
        $object_type_name_service = new ObjectTypeNameService($this->getModel(), $this->getColumnIdInReferenceTables());

        try {
            $id = $object_type_name_service->save($request);

            return $this->sendOK([
                'id' => $id
            ], self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        } catch (DuplicateNameException $e) {
            return $this->sendError(self::DUPLICATE_NAME, [], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $object_type_name_service = new ObjectTypeNameService($this->getModel(), $this->getColumnIdInReferenceTables());

        try {
            $object_type_name_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(self::RECORD_NO_FOUND, [], JsonResponse::HTTP_NOT_FOUND);
        } catch (ReferenceException $e) {
            return $this->sendError(self::RECORD_REFERENCE_FOUND, [], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkDuplicateName(Request $request): JsonResponse
    {
        $object_type_name_service = new ObjectTypeNameService($this->getModel(), $this->getColumnIdInReferenceTables());

        try {
            $object_type_name_service->checkDuplicateName($request);

            return $this->sendOK([]);
        } catch (DuplicateNameException $e) {
            return $this->sendError(self::DUPLICATE_NAME, [], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
