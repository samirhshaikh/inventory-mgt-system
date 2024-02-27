<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\RepairsDatatable;
use App\Datatables\SalesDatatable;
use App\Exceptions\InvalidDataException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveRepairRequest;
use App\Services\PurchaseService;
use App\Services\RepairsService;
use App\Services\SalesService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RepairsController extends BaseController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new RepairsDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.repairs.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.repairs.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $repairs_service = new RepairsService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $repairs_service->getAll(
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
    public function getSingle(IdRequest $request): JsonResponse
    {
        $repair_service = new RepairsService();

        try {
            $response = [];
            $response["record"] = $repair_service->getSingleRepair($request);

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
     * @param SaveRepairRequest $request
     * @return JsonResponse
     */
    public function save(SaveRepairRequest $request): JsonResponse
    {
        $repair_service = new RepairsService();

        try {
            $response = $repair_service->save($request);

            return $this->sendOK($response, self::RECORD_SAVED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (InvalidDataException $e) {
            return $this->sendError(
                self::INVALID_DATA,
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
        $repair_service = new RepairsService();

        try {
            $repair_service->delete($request);

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
