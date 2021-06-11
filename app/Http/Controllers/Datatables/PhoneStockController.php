<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\PhoneStockDatatable;
use App\Models\PhoneStock;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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

        $records = PhoneStock::selectRaw('PhoneStock.*, ManufactureMaster.Name, ColorMaster.Name, ModelMaster.Name')
            ->leftJoin('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
            ->leftJoin('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
            ->leftJoin('ModelMaster', 'ModelMaster.Id', '=', 'ModelId');

        if ($request->get("available_stock_only", 0)) {
            $records = $records->whereRaw('PhoneStock.Status != "Sold"');
        }

        $search_type = $request->get('search_type', 'simple');

        if ($search_type === 'simple' && $request->get('search_text', '') != '') {
            $fields_to_search = [
                'IMEI',
                'ManufactureMaster.Name',
                'ColorMaster.Name',
                'ModelMaster.Name',
                'Size',
                'Cost',
                'StockType',
                'ModelNo',
                'Network',
                'Status',
                'DATE_FORMAT(PhoneStock.CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(PhoneStock.UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $request->get('search_text'), 'AND');
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
                case 'make':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ManufactureMaster.Name', $search_text);
                    break;
                case 'model':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ModelMaster.Name', $search_text);
                    break;
                case 'color':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ColorMaster.Name', $search_text);
                    break;
                case 'IMEI':
                case 'StockType':
                case 'Status':
                case 'Size':
                case 'Network':
                case 'ModelNo':
                case 'Cost':
                    $model = $this->prepareAdvancedSearchQuery($model, $column, $search_text);
                    break;
                case 'UpdatedDate':
                    $model = $this->prepareAdvancedSearchQuery($model, ['DATE_FORMAT(PhoneStock.CreatedDate, "%d-%b-%Y")', 'DATE_FORMAT(PhoneStock.UpdatedDate, "%d-%b-%Y")'], $search_text, 'exact_match');
                    break;
            }
        }

        return $model;
    }
}
