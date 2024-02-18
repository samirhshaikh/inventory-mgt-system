<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\UsersDatatable;
use App\Exceptions\DuplicateNameException;
use App\Exceptions\NotEnoughRightsException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdStringRequest;
use App\Http\Requests\SaveUserRequest;
use App\Http\Requests\UserNameRequest;
use App\Services\UserService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UsersController extends BaseController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new UsersDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.users.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.users.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $user_service = new UserService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $user_service->getAll(
            $order_by,
            $order_direction,
            $request->get("search_text", "") ?? ""
        );

        return $this->prepareRecordsOutput(
            $table,
            $records,
            $total_records,
            (int) $request->get("page_no", 1),
            $request->get("search_text", ""),
            (int) $request->get("get_all_records", 0)
        );
    }

    /**
     * @param IdStringRequest $request
     * @return JsonResponse
     */
    public function changeActiveStatus(IdStringRequest $request): JsonResponse
    {
        $user_service = new UserService();

        try {
            $user_service->changeActiveStatus($request);

            return $this->sendOK([], "status_changed");
        } catch (NotEnoughRightsException $e) {
            return $this->sendError(
                "not_enough_rights",
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                "User not found.",
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param IdStringRequest $request
     * @return JsonResponse
     */
    public function changeAdminStatus(IdStringRequest $request): JsonResponse
    {
        $user_service = new UserService();

        try {
            $user_service->changeAdminStatus($request);

            return $this->sendOK([], "status_changed");
        } catch (NotEnoughRightsException $e) {
            return $this->sendError(
                "not_enough_rights",
                [],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                "User not found.",
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param UserNameRequest $request
     * @return JsonResponse
     */
    public function getSingle(UserNameRequest $request): JsonResponse
    {
        $user_service = new UserService();

        try {
            $response = [];
            $response["record"] = $user_service->getSingle($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                "User not found.",
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param SaveUserRequest $request
     * @return JsonResponse
     */
    public function save(SaveUserRequest $request): JsonResponse
    {
        $user_service = new UserService();

        try {
            $username = $user_service->save($request);

            return $this->sendOK(
                [
                    "username" => $username,
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

        return $this->sendOK([], self::RECORD_SAVED);
    }

    /**
     * @param UserNameRequest $request
     * @return JsonResponse
     */
    public function delete(UserNameRequest $request): JsonResponse
    {
        $user_service = new UserService();

        try {
            $user_service->delete($request);

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
     * @param UserNameRequest $request
     * @return JsonResponse
     */
    public function checkDuplicateName(UserNameRequest $request): JsonResponse
    {
        $user_service = new UserService();

        try {
            $user_service->checkDuplicateName($request);

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
