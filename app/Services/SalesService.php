<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\ReturnItemRequest;
use App\Models\PhoneStock;
use App\Models\Sales;
use App\Models\SalesStock;
use App\Models\StockLog;
use Carbon\Carbon;

class SalesService
{
    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingleSales(IdRequest $request)
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
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param ReturnItemRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
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

            //Mark the entry in salesstock as returned.
            $sale_stock = SalesStock::where('InvoiceId', $request->get('InvoiceId'))
                ->where('IMEI', $request->get('IMEI'));
            if ($sale_stock->get()->count()) {
                $sale_stock->Returned = true;
                $sale_stock->ReturnedDate = Carbon::createFromFormat('d-M-Y', $request->get('ReturnedDate'))->toDateTimeString();
                $sale_stock->save();
            }

            //Add an entry to stock_log
            $record = new StockLog();
            $record->IMEI = $request->get('IMEI');
            $record->Activity = StockLog::ACTIVITY_RETURNED;
            $record->LogDate = Carbon::createFromFormat('d-M-Y', $request->get('ReturnedDate'))->toDateTimeString();
            $record->Comments = $request->get('Comments');
            $record->CreatedBy = session('user_details.UserName');
            $record->UpdatedBy = session('user_details.UserName');
            $record->save();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }
}
