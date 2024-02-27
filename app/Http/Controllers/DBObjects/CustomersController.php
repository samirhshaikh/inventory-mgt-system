<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\CustomersDatatable;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveCustomerRequest;
use App\Services\CustomersService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CustomersController extends BaseController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new CustomersDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.customers.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.customers.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $customers_service = new CustomersService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $customers_service->getAll(
            $order_by,
            $order_direction,
            $request->get("search_type", "simple") ?? "simple",
            $request->get("search_text", "") ?? "",
            $request->get("search_data", "{}") ?? "{}"
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
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function changeActiveStatus(IdRequest $request): JsonResponse
    {
        $customers_service = new CustomersService();

        try {
            $customers_service->changeActiveStatus($request);

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
        $customers_service = new CustomersService();

        try {
            $response = [];
            $response["record"] = $customers_service->getSingle($request);

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
     * @param SaveCustomerRequest $request
     * @return JsonResponse
     */
    public function save(SaveCustomerRequest $request): JsonResponse
    {
        $customers_service = new CustomersService();

        try {
            $id = $customers_service->save($request);

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
        }
    }

    /**
     * @param IdRequest $request
     * @return JsonResponse
     */
    public function delete(IdRequest $request): JsonResponse
    {
        $customers_service = new CustomersService();

        try {
            $customers_service->delete($request);

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
}
