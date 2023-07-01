<?php

namespace App\Http\Controllers\Datatables;

use App\Models\CustomerSales;
use App\Datatables\CustomerSalesDatatable;
use App\Services\CustomerSalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class CustomerSalesController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new CustomerSalesDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.customer_sales.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.customer_sales.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $customer_sales_service = new CustomerSalesService();

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $customer_sales_service->getAll(
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
