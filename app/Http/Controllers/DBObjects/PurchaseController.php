<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\PurchasesDatatable;
use App\Exceptions\DuplicateIMEIException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\SavePurchaseRequest;
use App\Http\Requests\IdRequest;
use App\Services\PurchaseService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PurchaseController extends BaseController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new PurchasesDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.purchase.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.purchase.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $purchase_service = new PurchaseService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $purchase_service->getAll(
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
        $purchase_service = new PurchaseService();

        try {
            $response = [];
            $response["record"] = $purchase_service->getSinglePurchase(
                $request
            );

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
     * @param SavePurchaseRequest $request
     * @return JsonResponse
     */
    public function save(SavePurchaseRequest $request): JsonResponse
    {
        $purchase_service = new PurchaseService();

        try {
            $response = $purchase_service->save($request);

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
        } catch (ReferenceException $e) {
            return $this->sendError(
                self::RECORD_REFERENCE_FOUND,
                [],
                JsonResponse::HTTP_FORBIDDEN
            );
        } catch (DuplicateIMEIException $e) {
            return $this->sendError(
                self::DUPLICATE_IMEI,
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
        $purchase_service = new PurchaseService();

        try {
            $purchase_service->delete($request);

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
