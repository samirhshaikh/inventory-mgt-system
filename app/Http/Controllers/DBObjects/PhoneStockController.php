<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\PhoneStockDatatable;
use App\Exceptions\DuplicateIMEIException;
use App\Exceptions\InvalidIMEIException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\IMEIRequest;
use App\Services\PhoneStockService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PhoneStockController extends BaseController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new PhoneStockDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.phonestock.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.phonestock.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        if (in_array($order_by, ["UpdatedDate"])) {
            $order_by = "PhoneStock.UpdatedDate";
        }

        $phonestock_service = new PhoneStockService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $phonestock_service->getAll(
            $order_by,
            $order_direction,
            $request->get("search_type", "simple") ?? "simple",
            $request->get("search_text", "") ?? "",
            $request->get("search_data", "{}") ?? "{}",
            $request->get("available_stock_only", 0) != 0
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
     * @param Request $request
     * @return array
     */
    public function getAvailablePhoneData(Request $request): array
    {
        $request->merge([
            "available_stock_only" => 1,
            "order_by" => "IMEI",
        ]);

        return $this->getData($request);
    }

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
