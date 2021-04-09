<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\SalesDatatable;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends BaseDatatableController
{
    public function getData(Request $request)
    {
//        DB::enableQueryLog();
        $table = new SalesDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.sales.column', array_get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.sales.direction', array_get($table->options(), 'sorting.direction'))
            : 'asc';

        $invoice_ids = null;
        $search_type = $request->get('search_type', 'simple');
        if (
            ($search_type === 'simple' && $request->get('search_text', '') != '') ||
            ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', [])))
        ) {
            $invoice_ids = $this->getInvoiceIds($request);
        }

        $records = new Sales();

        $records = $records->leftjoin('Customer', 'Customer.Id', '=', 'CustomerId');

        $records = $records->with(['sales' => function ($query) {
            $query->orderBy('IMEI', 'ASC');
        }]);

        if (!is_null($invoice_ids)) {
            $records = $records->whereIn('Sales.Id', $invoice_ids);
        }

        switch ($order_by) {
            case 'IMEI':
                break;
            case 'childs':
                $records = $records->addSelect(DB::raw('*, SUM(SalesStock.Cost) as Total_Cost'))
                    ->leftJoin('SalesStock', 'SalesStock.InvoiceId', '=', 'Sales.Id')
                    ->groupBy('Sales.Id')
                    ->orderBy('Total_Cost', $order_direction);
                break;
            case 'customer.CustomerName':
                $records = $records->leftJoin('Customer', 'Customer.Id', '=', 'CustomerId');
                break;
            case 'UpdatedDate':
                $records = $records->orderBy('Sales.UpdatedDate', $order_direction);
                break;
            default:
                $records = $records->orderBy($order_by, $order_direction);
        }

        $records = $records->select('Sales.*');

//        $records = $records->get()->all();
//        dd(DB::getQueryLog());

        return $this->prepareRecordsOutput(
            $table,
            $records,
            $request->get('page_no', 1),
            $request->get('search_text', ''),
            $request->get('get_all_records', 0)
        );
    }

    public function getInvoiceIds(Request $request)
    {
        $records = Sales::selectRaw('Sales.Id');

        $search_type = $request->get('search_type', 'simple');

        if (
            ($search_type === 'simple' && $request->get('search_text', '') != '') ||
            ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', [])))
        ) {
            $records = $records
                ->leftjoin('Customer', 'Customer.Id', '=', 'CustomerId')
                ->join('SalesStock', 'SalesStock.InvoiceId', '=', 'Sales.Id')
                ->join('PhoneStock', 'PhoneStock.IMEI', '=', 'SalesStock.IMEI')
                ->join('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
                ->join('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
                ->join('ModelMaster', 'ModelMaster.Id', '=', 'ModelId')
            ;
        }

        if ($search_type === 'simple' && $request->get('search_text', '') != '') {
            $fields_to_search = [
                'InvoiceNo',
                'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                'Customer.CustomerName',
                'IMEI',
                'ManufactureMaster.Name',
                'ColorMaster.Name',
                'ModelMaster.Name',
                'Size',
                'Cost',
                'ModelNo',
                'Network',
                'Sales.Comments',
                'DATE_FORMAT(Sales.CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(Sales.UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $request->get('search_text'));
        } else if ($search_type === 'advanced' && count($request->get('search_data', []))) {
            $records = $this->prepareAdvancedSearch($records, collect($request->get('search_data'))->all());

//            dd($this->getSql($records));
        }

        $records = $records->orderBy('Sales.Id', 'ASC')
            ->get();

        return $records->pluck('Id')->all();
    }

    protected function prepareAdvancedSearch($model, $search_data = [])
    {
        foreach ($search_data as $column => $search_text) {
            if ($search_text == '' || is_null($search_text)) {
                continue;
            }

            switch ($column) {
                case 'customer':
                    $model = $this->prepareAdvancedSearchQuery($model, 'Customer.CustomerName', $search_text);
                    break;
                case 'contact':
                    $model = $this->prepareAdvancedSearchQuery($model, ['Customer.ContactNo1', 'Customer.ContactNo2', 'Customer.ContactNo3'], $search_text);
                    break;
                case 'InvoiceDate':
                    $model = $this->prepareAdvancedSearchQuery($model, 'DATE_FORMAT(Sales.InvoiceDate, "%d-%b-%Y")', $search_text, 'exact_match');
                    break;
                case 'manufacturer':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ManufactureMaster.Name', $search_text);
                    break;
                case 'model':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ModelMaster.Name', $search_text);
                    break;
                case 'color':
                    $model = $this->prepareAdvancedSearchQuery($model, 'ColorMaster.Name', $search_text);
                    break;
                case 'InvoiceNo':
                case 'IMEI':
                case 'Cost':
                    $model = $this->prepareAdvancedSearchQuery($model, 'Sales.' . $column, $search_text);
                    break;
                case 'PaymentMethod':
                    $model = $this->prepareAdvancedSearchQuery($model, 'Sales.' . $column, $search_text, 'exact_match');
                    break;
                case 'Size':
                case 'Network':
                case 'ModelNo':
                    $model = $this->prepareAdvancedSearchQuery($model, 'SalesStock.' . $column, $search_text);
                    break;
                case 'UpdatedDate':
                    $model = $this->prepareAdvancedSearchQuery($model, ['DATE_FORMAT(Sales.CreatedDate, "%d-%b-%Y")', 'DATE_FORMAT(Sales.UpdatedDate, "%d-%b-%Y")'], $search_text, 'exact_match');
                    break;
            }
        }

        return $model;
    }
}
