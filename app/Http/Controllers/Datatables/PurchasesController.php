<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\PurchasesDatatable;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PurchasesController extends BaseDatatableController
{
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
}
