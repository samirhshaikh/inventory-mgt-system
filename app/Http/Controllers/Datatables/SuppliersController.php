<?php

namespace App\Http\Controllers\Datatables;

use App\Models\Suppliers;
use App\Datatables\SuppliersDatatable;
use Illuminate\Http\Request;

class SuppliersController extends BaseDatatableController
{
    public function getData(Request $request)
    {
        $table = new SuppliersDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.suppliers.column', array_get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.suppliers.direction', array_get($table->options(), 'sorting.direction'))
            : 'asc';

        $records = new Suppliers();

        $search_type = $request->get('search_type', 'simple');

        if ($search_type === 'simple' && $request->get('search_text', '') != '') {
            $fields_to_search = [
                'SupplierName',
                'ContactNo1',
                'ContactNo2',
                'ContactNo3',
                'CurrentBalance',
                'Comments',
                'DATE_FORMAT(CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $request->get('search_text'));
        } else if ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', []))) {
            $records = $this->prepareAdvancedSearch($records, collect($request->get('search_data'))->all());
        }

        $records = $records->orderBy($order_by, $order_direction);

        return $this->prepareRecordsOutput(
            $table,
            $records,
            $request->get('page_no', 1),
            $request->get('search_text', ''),
            $request->get('get_all_records', 0)
        );
    }

    protected function prepareAdvancedSearch($model, $search_data = [])
    {
        foreach ($search_data as $column => $search_text) {
            if ($search_text == '' || is_null($search_text)) {
                continue;
            }

            switch ($column) {
                case 'SupplierName':
                case 'CurrentBalance':
                    $model = $this->prepareAdvancedSearchQuery($model, $column, $search_text);
                    break;
                case 'ContactNo':
                    $model = $this->prepareAdvancedSearchQuery($model, ['ContactNo1', 'ContactNo2', 'ContactNo3'], $search_text);
                    break;
                case 'UpdatedDate':
                    $model = $this->prepareAdvancedSearchQuery($model, ['DATE_FORMAT(CreatedDate, "%d-%b-%Y")', 'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")'], $search_text, 'exact_match');
                    break;
            }
        }

        return $model;
    }
}
