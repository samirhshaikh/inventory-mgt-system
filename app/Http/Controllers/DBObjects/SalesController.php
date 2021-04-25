<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ReturnItemRequest;
use App\Models\PhoneStock;
use App\Models\Sales;
use App\Models\SalesStock;
use App\Models\StockLog;
use App\Traits\TableActions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesController extends BaseController
{
    use TableActions;

    public function getSingle(Request $request)
    {
        $record = Sales::where('Sales.Id', $request->get('Id'))
            ->with('customer')
            ->with('sales')
            ->where('Id', $request->get('Id'))
            ->get();

//        $record = Sales::selectRaw('Sales.*,
//        Customer.CustomerName as customer,
//        Customer.ContactNo1 as ContactNo1,
//        Customer.ContactNo2 as ContactNo2,
//        Customer.ContactNo3 as ContactNo3,
//        PhoneStock.Id as PhoneStockId,
//        ManufactureMaster.Name as manufacturer,
//        ColorMaster.Name as color,
//        ModelMaster.Name as model')
//            ->where('Sales.Id', $request->get('Id'))
//            ->join('salesstock', 'InvoiceId', 'Sales.Id')
//            ->leftJoin('Customer', 'Customer.Id', '=', 'CustomerId')
//            ->leftJoin('PhoneStock', 'PhoneStock.IMEI', '=', 'salesstock.IMEI')
//            ->leftJoin('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
//            ->leftJoin('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
//            ->leftJoin('ModelMaster', 'ModelMaster.Id', '=', 'ModelId')
//            ->get()
//        ;

        if ($record->count()) {
            $response = [];
            $response['record'] = $record->map->transform()->first();
            return $this->sendOK($response);
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function returnItem(ReturnItemRequest $request)
    {
        $record = PhoneStock::where('IMEI', $request->get('IMEI'))
            ->get();

        if ($record->count()) {
            //Change the status in phonestock
            $record = $record->first();
            $record->Status = PhoneStock::STATUS_IN_STOCK;
            $record->UpdatedBy = session('user_details.UserName');
            $record->save();

            //Remove the entry from Sales
            $sale_stock = SalesStock::where('InvoiceId', $request->get('InvoiceId'))
                ->where('IMEI', $request->get('IMEI'));
            if ($sale_stock->get()->count()) {
                $sale_stock->delete();
            }
            //Check whether the given invoice has got any other item or not. If not then remove the invoice as well.
            $sale_stock = SalesStock::where('InvoiceId', $request->get('InvoiceId'))
                ->get();
            if ($sale_stock->count() === 0) {
                Sales::where('Id', $request->get('InvoiceId'))
                    ->delete();
            }

            //Add an entry to stock_log
            $record = new StockLog();
            $record->IMEI = $request->get('IMEI');
            $record->Activity = StockLog::ACTIVITY_RETURNED;
            $record->LogDate = Carbon::createFromFormat('d-M-Y', $request->get('ReturnDate'))->toDateTimeString();
            $record->Comments = $request->get('Comments');
            $record->CreatedBy = session('user_details.UserName');
            $record->UpdatedBy = session('user_details.UserName');
            $record->save();

            return $this->sendOK([], 'record_saved');
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }
}
