<?php

namespace App\Http\Controllers\Datatables;

use App\Models\CustomerSales;
use App\Datatables\CustomerSalesDatatable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CustomerSalesController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new CustomerSalesDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.customer_sales.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.customer_sales.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        $records = new CustomerSales();

        $search_type = $request->get('search_type', 'simple');

        if ($search_type === 'simple' && $request->get('search_text', '') != '') {
            $columns_to_search = [
                'CustomerName',
                'ContactNo1',
                'ContactNo2',
                'Address',
                'City',
                'Balance',
                'Comments',
                'DATE_FORMAT(CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $columns_to_search, $request->get('search_text'));
        } else if ($search_type === 'advanced' && count(json_decode($request->get('search_data', '{}')))) {
            $records = $this->prepareAdvancedSearch($records, json_decode($request->get('search_data')));
        }

        //Get total records
        $all_records = $records->addSelect(DB::raw('COUNT(*) as Record_Count'))
            ->get()
            ->first()
        ;

        $records = $records->orderBy($order_by, $order_direction);

        return $this->prepareRecordsOutput(
            $table,
            $records,
            $all_records['Record_Count'],
            (int)$request->get('page_no', 1),
            $request->get('search_text', ''),
            (int)$request->get('get_all_records', 0)
        );
    }

    /**
     * @param $model
     * @param array $search_data
     * @return Builder
     */
    protected function prepareAdvancedSearch($model, $search_data = []): Builder
    {
        foreach ($search_data as $column => $search_text) {
            if ($search_text == '' || is_null($search_text)) {
                continue;
            }

            switch ($column) {
                case 'CustomerName':
                    $model = $this->prepareAdvancedSearchQuery($model, $column, $search_text);
                    break;
                case 'ContactNo':
                    $model = $this->prepareAdvancedSearchQuery($model, ['ContactNo1', 'ContactNo2'], $search_text);
                    break;
                case 'UpdatedDate':
                    $model = $this->prepareAdvancedSearchQuery($model, ['DATE_FORMAT(CreatedDate, "%d-%b-%Y")', 'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")'], $search_text, 'exact_match');
                    break;
            }
        }

        return $model;
    }
}
