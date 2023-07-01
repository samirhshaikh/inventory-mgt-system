<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\SalesDatatable;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SalesController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new SalesDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.sales.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.sales.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $sales_service = new SalesService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $sales_service->getAll(
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
}
