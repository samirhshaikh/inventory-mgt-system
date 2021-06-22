<?php

namespace App\Http\Controllers\Datatables;

use App\Models\Suppliers;
use App\Datatables\SuppliersDatatable;
use App\Services\SuppliersService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class SuppliersController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new SuppliersDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.suppliers.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.suppliers.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        $suppliers_service = new SuppliersService();

        list('total_records' => $total_records, 'records' => $records) = $suppliers_service->getAll(
            $order_by,
            $order_direction,
            $request->get('search_type', 'simple') ?? 'simple',
            $request->get('search_text', '') ?? '',
            $request->get('search_data', '{}') ?? '{}'
        );

        return $this->prepareRecordsOutput(
            $table,
            $records,
            $total_records,
            (int)$request->get('page_no', 1),
            $request->get('search_text', ''),
            (int)$request->get('get_all_records', 0)
        );
    }
}
