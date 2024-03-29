<?php

namespace App\Http\Controllers\DBObjects;

use App\Datatables\HandsetColorsDatatable;
use App\Models\HandsetColors;
use App\Services\ObjectTypeNameService;
use App\Traits\DataOutputTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HandsetColorsController extends ObjectTypeNameController
{
    use DataOutputTrait;

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new HandsetColorsDatatable();

        $order_by =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.handset_colors.column",
                    Arr::get($table->options(), "sorting.default")
                )
                : $request->get("order_by");
        $order_direction =
            $request->get("order_by", "") == ""
                ? session(
                    "app_settings.datatable.sorting.handset_colors.direction",
                    Arr::get($table->options(), "sorting.direction")
                )
                : "asc";

        $object_type_name_service = new ObjectTypeNameService(
            new HandsetColors(),
            $this->getColumnIdInReferenceTables()
        );

        list(
            "total_records" => $total_records,
            "records" => $records,
        ) = $object_type_name_service->getAll(
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

    protected function getModel()
    {
        return new HandsetColors();
    }

    protected function getRecordName()
    {
        return "Color";
    }

    protected function getColumnIdInReferenceTables()
    {
        return "ColorId";
    }

    protected function getTablesToCheck()
    {
        return ["PhoneStock", "Handsets"]; //'Repair';
    }
}
