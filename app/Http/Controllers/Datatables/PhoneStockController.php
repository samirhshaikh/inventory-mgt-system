<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\PhoneStockDatatable;
use App\Models\PhoneStock;
use App\Services\PhoneStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class PhoneStockController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new PhoneStockDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.phonestock.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.phonestock.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        if (in_array($order_by, ['UpdatedDate'])) {
            $order_by = 'PhoneStock.UpdatedDate';
        }

        $phonestock_service = new PhoneStockService();

        list('total_records' => $total_records, 'records' => $records) = $phonestock_service->getAll(
            $order_by,
            $order_direction,
            $request->get('search_type', 'simple') ?? 'simple',
            $request->get('search_text', '') ?? '',
            $request->get('search_data', '{}') ?? '{}',
            $request->get('available_stock_only', 0) != 0
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

    /**
     * @param Request $request
     * @return array
     */
    public function getAvailablePhoneData(Request $request): array
    {
        $request->merge([
            "available_stock_only" => 1,
            "order_by" => "IMEI"
        ]);

        return $this->getData($request);
    }
}
