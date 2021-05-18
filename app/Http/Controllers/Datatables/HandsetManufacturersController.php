<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\HandsetManufacturersDatatable;
use App\Models\HandsetManufacturers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HandsetManufacturersController extends BaseDatatableController
{
    public function getData(Request $request)
    {
        $table = new HandsetManufacturersDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.handset_manufacturers.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.handset_manufacturers.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        $records = new HandsetManufacturers();

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
