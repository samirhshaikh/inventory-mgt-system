<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\HandsetColorsDatatable;
use App\Models\HandsetColors;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HandsetColorsController extends BaseDatatableController
{
    public function getData(Request $request)
    {
        $table = new HandsetColorsDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.handset_colors.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.handset_colors.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        $records = new HandsetColors();

        if ($request->get('search_text', '') != '') {
            $fields_to_search = [
                'Name',
                'DATE_FORMAT(CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $request->get('search_text'));
        }

        $records = $records->orderBy($order_by, $order_direction);

        return $this->prepareRecordsOutput(
            $table,
            $records,
            (int)$request->get('page_no', 1),
            $request->get('search_text', ''),
            (int)$request->get('get_all_records', 0)
        );
    }
}
