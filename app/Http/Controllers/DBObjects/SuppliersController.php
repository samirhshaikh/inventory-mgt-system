<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\PartSuppliersDatatable;
use App\Datatables\SuppliersDatatable;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveSupplierRequest;
use App\Services\SuppliersService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class SuppliersController extends BaseController
{
    use DataOutputTrait;

    public string $table_name;

    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * @param Request $request
     * @param $datatable
     * @return array
     */
    protected function getData(Request $request): array
    {
        $datatable = match ($this->table_name) {
            "suppliers" => new SuppliersDatatable(),
            "parts_suppliers" => new PartSuppliersDatatable(),
            default => null,
        };
        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.{$this->table_name}.column",
                    Arr::get($datatable->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.{$this->table_name}.direction",
                    Arr::get($datatable->options(), "sorting.direction")
                )
                : "asc";

        $suppliers_service = new SuppliersService($this->table_name);

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $suppliers_service->getAll(
            $order_by,
            $order_direction,
            $request->get("search_type", "simple") ?? "simple",
            $request->get("search_text", "") ?? "",
            $request->get("search_data", "{}") ?? "{}"
        );

        return $this->prepareRecordsOutput(
            $datatable,
            $records,
            $total_records,
            (int) $request->get("page_no", 1),
            $request->get("search_text", ""),
            (int) $request->get("get_all_records", 0)
        );
    }

    /**
     * @param IdRequest $request
     * @return Response
     */
    protected function changeActiveStatus(IdRequest $request): Response
    {
        $suppliers_service = new SuppliersService($this->table_name);

        try {
            $suppliers_service->changeActiveStatus($request);

            return $this->sendOK([], "status_changed");
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return Response
     */
    protected function getSingle(IdRequest $request): Response
    {
        $suppliers_service = new SuppliersService($this->table_name);

        try {
            $response = [];
            $response["record"] = $suppliers_service->getSingle($request);

            return $this->sendOK($response);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param SaveSupplierRequest $request
     * @return Response
     */
    protected function save(SaveSupplierRequest $request): Response
    {
        $suppliers_service = new SuppliersService($this->table_name);

        try {
            $id = $suppliers_service->save($request);

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
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @param IdRequest $request
     * @return Response
     */
    protected function delete(IdRequest $request): Response
    {
        $suppliers_service = new SuppliersService($this->table_name);

        try {
            $suppliers_service->delete($request);

            return $this->sendOK([], self::RECORD_DELETED);
        } catch (RecordNotFoundException $e) {
            return $this->sendError(
                self::RECORD_NO_FOUND,
                [],
                Response::HTTP_NOT_FOUND
            );
        } catch (ReferenceException $e) {
            return $this->sendError(
                self::RECORD_REFERENCE_FOUND,
                [],
                Response::HTTP_FORBIDDEN
            );
        }
    }
}
