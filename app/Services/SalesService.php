<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\ReturnItemRequest;
use App\Http\Requests\SaveSaleRequest;
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
     * @param SaveSaleRequest $request
     * @return int
     * @throws RecordNotFoundException
     */
    public function save(SaveSaleRequest $request): int
    {
        /*
         * First we will check for any errors. If yes then return the error code.
         * If no error is found then perform the operation.
         */

        //Check whether the sale being edited exist or not.
        if ($request->get('operation', 'add') == 'edit') {
            $record = Sales::where('Id', $request->get('Id'))->get();
            if (!$record->count()) {
                throw new RecordNotFoundException;
            }
        }

        //Now delete the child phones
        if (
            $request->get('operation', 'add') == 'edit' &&
            count($request->get('children_to_delete', []))
        ) {
            $phonestock_service = new PhoneStockService();

            foreach ($request->get('children_to_delete', []) as $row) {
                $phone = SalesStock::where('Id', $row['Id'])
                    ->get();
                if ($phone->count()) {
                    $phone = $phone->first();

                    //Add an entry to stock_log
                    $stocklog_service = new StockLogService();
                    $stocklog_service->add(
                        $phone['IMEI'],
                        StockLog::ACTIVITY_DELETED
                    );

                    //Change the status in phonestock
                    $phonestock_service->changePhoneAvailabilityStatus($row['IMEI']);

                    SalesStock::where('Id', $row['Id'])->delete();
                }
            }
        }

        //Create new record in sales table
        if ($request->get('operation', 'add') == 'edit') {
            $record = $record->first();
        } else {
            $record = new Sales();

            $record->InvoiceNo = $this->getNextInvoiceNo();
            $record->CreatedBy = session('user_details.UserName');
            $record->IsActive = 1;
        }

        $record->InvoiceDate = Carbon::createFromFormat('d-M-Y', $request->get('InvoiceDate'))->toDateTimeString();
        $record->CustomerId = $request->get('CustomerId');
        $record->PaymentMethod = $request->get('PaymentMethod');
        $record->VAT = number_format($request->get('VAT', 0), 2);
        $record->Comments = $request->get('Comments');
        $record->UpdatedBy = session('user_details.UserName');
        $record->save();

        if ($request->get('operation', 'add') == 'add') {
            $record->Id = Sales::lastInsertId();
        }

        $salesstock_service = new SalesStockService();

        //Create/Update records in salesstock table
        $salesstock_service->save($record->Id, $request->get('children', []));

        return $record->Id;
    }

    /**
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    public function delete(IdRequest $request): bool
    {
        //Check whether the record exist or not
        $invoice = Sales::where('Id', $request->get('Id'))->get();

        if ($invoice->count()) {
            $phonestock_service = new PhoneStockService();

            $invoice = $invoice->first();

            //Get all the phones in this invoice
            $phones = SalesStock::where('InvoiceId', $invoice->Id)->get();
            if ($phones->count()) {
                foreach ($phones as $phone) {
                    //Add an entry to stock_log
                    $stocklog_service = new StockLogService();
                    $stocklog_service->add(
                        $phone['IMEI'],
                        StockLog::ACTIVITY_DELETED
                    );

                    //Change the status in phonestock
                    $phonestock_service->changePhoneAvailabilityStatus($phone['IMEI']);

                    SalesStock::where('Id', $phone->Id)->delete();
                }
            }

            Sales::where('Id', $request->get('Id'))->delete();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param ReturnItemRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    public function returnItem(ReturnItemRequest $request): bool
    {
        $phonestock_service = new PhoneStockService();

        try {
            //Change the status in phonestock
            $phonestock_service->changePhoneAvailabilityStatus($request->get('IMEI'));

            //Mark the entry in salesstock as returned.
            $sale_stock = SalesStock::where('InvoiceId', $request->get('InvoiceId'))
                ->where('IMEI', $request->get('IMEI'))
                ->get();
            if ($sale_stock->count()) {
                $sale_stock = $sale_stock->first();

                $sale_stock->Returned = true;
                $sale_stock->ReturnedDate = Carbon::createFromFormat('d-M-Y', $request->get('ReturnedDate'))->toDateTimeString();
                $sale_stock->Comments = $request->get('Comments');
                $sale_stock->save();
            }

            //Add an entry to stock_log
            $stocklog_service = new StockLogService();
            $stocklog_service->add(
                $request->get('IMEI'),
                StockLog::ACTIVITY_RETURNED,
                $request->get('Comments')
            );

            return true;
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @return int
     */
    public function getNextInvoiceNo(): int
    {
        $today = Carbon::now()->format('Y-m-d');

        $record = Sales::selectRaw('Sales.*')
            ->whereRaw('InvoiceDate LIKE "%' . $today . '%"')
            ->get()
        ;

        return $record->count() + 1;
    }
}
