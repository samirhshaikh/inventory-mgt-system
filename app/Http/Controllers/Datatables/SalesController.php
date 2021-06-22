<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\SalesDatatable;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class SalesController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
//        DB::enableQueryLog();
        $table = new SalesDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.sales.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.sales.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        $invoice_ids = null;
        $search_type = $request->get('search_type', 'simple');
        if (
            ($search_type === 'simple' && $request->get('search_text', '') != '') ||
            ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', '{}')))
        ) {
            $invoice_ids = $this->getInvoiceIds($request);
        }

        $records = new Sales();

        $records = $records->leftjoin('Customer_Sales', 'Customer_Sales.Id', '=', 'CustomerId');

        if (!is_null($invoice_ids)) {
            $records = $records->whereIn('Sales.Id', $invoice_ids);
        }

        //Get total records
        $total_records = $this->getTotalRecords(clone $records);

        $records = $records->with(['sales' => function ($query) {
            $query->orderBy('IMEI', 'ASC');
        }]);

        switch ($order_by) {
            case 'IMEI':
                break;
            case 'children':
                $records = $records->addSelect(DB::raw('SUM(CASE WHEN Return = 0 THEN 0 ELSE SalesStock.Cost) as Total_Cost'))
                    ->leftJoin('SalesStock', 'SalesStock.InvoiceId', '=', 'Sales.Id')
                    ->groupBy('Sales.Id')
                    ->orderBy('Total_Cost', $order_direction);
                break;
            case 'customer.CustomerName':
                $records = $records->leftJoin('Customer_Sales', 'Customer_Sales.Id', '=', 'CustomerId');
                break;
            case 'UpdatedDate':
                $records = $records->orderBy('Sales.UpdatedDate', $order_direction);
                break;
            default:
                $records = $records->orderBy($order_by, $order_direction);
        }

        $records = $records->addSelect('Sales.*');

//        $records = $records->get()->all();
//        dd(DB::getQueryLog());

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
    public function getInvoiceIds(Request $request): array
    {
        $records = Sales::selectRaw('Sales.Id');

        $search_type = $request->get('search_type', 'simple');

        if (
            ($search_type === 'simple' && $request->get('search_text', '') != '') ||
            ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', '{}')))
        ) {
            $records = $records
                ->leftjoin('Customer_Sales', 'Customer_Sales.Id', '=', 'CustomerId')
                ->join('SalesStock', 'SalesStock.InvoiceId', '=', 'Sales.Id')
                ->join('PhoneStock', 'PhoneStock.IMEI', '=', 'SalesStock.IMEI')
                ->join('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
                ->join('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
                ->join('ModelMaster', 'ModelMaster.Id', '=', 'ModelId');
        }

        if ($search_type === 'simple' && $request->get('search_text', '') != '') {
            $fields_to_search = [
                $this->getInvoiceSearchString(),
                'InvoiceNo',
                'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                'Customer_Sales.CustomerName',
                'SalesStock.IMEI',
                'ManufactureMaster.Name',
                'ColorMaster.Name',
                'ModelMaster.Name',
                'Size',
                'SalesStock.Cost',
                'ModelNo',
                'Network',
                'Sales.Comments',
                'DATE_FORMAT(Sales.CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(Sales.UpdatedDate, "%d-%b-%Y")'
            ];

            $records = $this->prepareSearch($records, $fields_to_search, $request->get('search_text'));
        } else if ($search_type === 'advanced' && $this->searchDataPresent($request->get('search_data', '{}'))) {
            $records = $this->prepareAdvancedSearch($records, json_decode($request->get('search_data')));

//            dd($this->getSql($records));
        }

        $records = $records->orderBy('Sales.Id', 'ASC')
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
                case 'customer':
                    $model = $this->prepareAdvancedSearchQuery($model, 'Customer_Sales.CustomerName', $search_text);
                    break;
                case 'contact':
                    $model = $this->prepareAdvancedSearchQuery($model, ['Customer_Sales.ContactNo1', 'Customer_Sales.ContactNo2'], $search_text);
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
                    $model = $this->prepareAdvancedSearchQuery($model, ['InvoiceNo', $this->getInvoiceSearchString()], $search_text);
                    break;
                case 'IMEI':
                    $model = $this->prepareAdvancedSearchQuery($model, 'SalesStock.IMEI', $search_text);
                    break;
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

    /**
     * For new invoice number we have a different format. INV-YYYY-MM-DD-XXXX where XXXX represents a four digit invoice number with leading zeros.
     * We need to special mysql string to get that in search
     *
     * @return string
     */
    private function getInvoiceSearchString(): string
    {
        return 'IF(InvoiceNo REGEXP "^-?[0-9]+$", CONCAT("INV-", DATE_FORMAT(InvoiceDate, "%Y-%m-%d"), "-", LPAD(InvoiceNo, 4, "0")), "")';
    }
}
