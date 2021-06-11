<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\PurchasesDatatable;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class PurchasesController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new PurchasesDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.purchase.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.purchase.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        $invoice_ids = null;
        $search_type = $request->get('search_type', 'simple');
        if (
            ($search_type === 'simple' && $request->get('search_text', '') != '') ||
            ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', '{}')))
        ) {
            $invoice_ids = $this->getInvoiceIds($request);
        }

        $records = new Purchase();

        $records = $records->with('supplier');

        $records = $records->with(['purchases' => function ($query) {
            $query->orderBy('IMEI', 'ASC');
        }]);

        if (!is_null($invoice_ids)) {
            $records = $records->whereIn('Purchase.Id', $invoice_ids);
        }

        //Get total records
        $all_records = $records->addSelect(DB::raw('COUNT(*) as Record_Count'))
            ->get()
            ->first()
        ;

        switch ($order_by) {
            case 'IMEI':
                break;
            case 'children':
                $records = $records->addSelect(DB::raw('*, SUM(PhoneStock.Cost) as Total_Cost'))
                    ->leftJoin('PhoneStock', 'PhoneStock.InvoiceId', '=', 'Purchase.Id')
                    ->groupBy('Purchase.Id')
                    ->orderBy('Total_Cost', $order_direction);
                break;
            case 'supplier.SupplierName':
                $records = $records->leftJoin('Supplier', 'Supplier.Id', '=', 'SupplierId');
                break;
            default:
                $records = $records->orderBy($order_by, $order_direction);
        }

        $records = $records->select('Purchase.*');

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
    public function getDataUsingJoin(Request $request): array
    {
        $table = new PurchasesDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.purchase.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.purchase.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        if (in_array($order_by, ['UpdatedDate'])) {
            $order_by = 'Purchase.UpdatedDate';
        }

        $records = Purchase::selectRaw('
        Purchase.*,
        Supplier.SupplierName as supplier,
        IMEI, Size, Cost, StockType, ModelNo, Network,
        ManufactureMaster.Name, ColorMaster.Name, ModelMaster.Name')
            ->leftJoin('Supplier', 'Supplier.Id', '=', 'SupplierId')
            ->leftJoin('PhoneStock', 'PhoneStock.InvoiceId', '=', 'Purchase.Id')
            ->leftJoin('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
            ->leftJoin('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
            ->leftJoin('ModelMaster', 'ModelMaster.Id', '=', 'ModelId');

        $search_type = $request->get('search_type', 'simple');

        if ($search_type === 'simple' && $request->get('search_text', '') != '') {
            $fields_to_search = [
                'InvoiceNo',
                'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                'Supplier.SupplierName',
                'IMEI',
                'ManufactureMaster.Name',
                'ColorMaster.Name',
                'ModelMaster.Name',
                'Size',
                'Cost',
                'StockType',
                'ModelNo',
                'Network',
                'Purchase.Comments',
                'Status',
                'DATE_FORMAT(Purchase.CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(Purchase.UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $request->get('search_text'));
        } else if ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', '{}'))) {
            $records = $this->prepareAdvancedSearch($records, json_decode($request->get('search_data')));
        }

        //Get total records
        $all_records = $records->addSelect(DB::raw('COUNT(*) as Record_Count'))
            ->get()
            ->first()
        ;

        //If ordering on child records, then add the InvoiceNo as primary order by
        if ($order_by === "phones") {
            $records = $records->orderBy("InvoiceNo", "ASC");
        }

        $records = $records->orderBy($order_by, $order_direction);

        $records = $records->select('Purchase.*');

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
    public function getInvoiceIds(Request $request): array
    {
        $records = Purchase::selectRaw('Purchase.Id');

        $search_type = $request->get('search_type', 'simple');

        if (
            ($search_type === 'simple' && $request->get('search_text', '') != '') ||
            ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', '{}')))
        ) {
            $records = $records
                ->join('Supplier', 'Supplier.Id', '=', 'SupplierId')
                ->join('PhoneStock', 'PhoneStock.InvoiceId', '=', 'Purchase.Id')
                ->join('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
                ->join('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
                ->join('ModelMaster', 'ModelMaster.Id', '=', 'ModelId');
        }

        if ($search_type === 'simple' && $request->get('search_text', '') != '') {
            $fields_to_search = [
                'InvoiceNo',
                'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                'Supplier.SupplierName',
                'IMEI',
                'ManufactureMaster.Name',
                'ColorMaster.Name',
                'ModelMaster.Name',
                'Size',
                'Cost',
                'StockType',
                'ModelNo',
                'Network',
                'Purchase.Comments',
                'Status',
                'DATE_FORMAT(Purchase.CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(Purchase.UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $request->get('search_text'));
        } else if ($search_type === 'advanced' && count(json_decode($request->get('search_data', '{}')))) {
            $records = $this->prepareAdvancedSearch($records, json_decode($request->get('search_data')));
        }

        $records = $records->orderBy('Purchase.Id', 'ASC')
            ->get();

        return $records->pluck('Id')->all();
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
                case 'supplier':
                    $model = $this->prepareAdvancedSearchQuery($model, 'Supplier.SupplierName', $search_text);
                    break;
                case 'InvoiceDate':
                    $model = $this->prepareAdvancedSearchQuery($model, 'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")', $search_text, 'exact_match');
                    break;
                case 'make':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ManufactureMaster.Name', $search_text);
                    break;
                case 'model':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ModelMaster.Name', $search_text);
                    break;
                case 'color':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ColorMaster.Name', $search_text);
                    break;
                case 'phones':
                    $model = $this->prepareAdvancedSearchQuery($model, 'PhoneStock.IMEI', $search_text);
                    break;
                case 'IMEI':
                case 'InvoiceNo':
                case 'StockType':
                case 'Status':
                case 'Size':
                case 'Network':
                case 'ModelNo':
                case 'Cost':
                    $model = $this->prepareAdvancedSearchQuery($model, $column, $search_text);
                    break;
                case 'UpdatedDate':
                    $model = $this->prepareAdvancedSearchQuery($model, ['DATE_FORMAT(Purchase.CreatedDate, "%d-%b-%Y")', 'DATE_FORMAT(Purchase.UpdatedDate, "%d-%b-%Y")'], $search_text, 'exact_match');
                    break;
            }
        }

        return $model;
    }
}
