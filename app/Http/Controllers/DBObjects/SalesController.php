<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Models\Sales;
use App\Traits\TableActions;

class SalesController extends BaseController
{
    use TableActions;

    public function changeActiveStatus(Request $request)
    {
        $message = $this->changeRecordStatus(new Sales, $request);

        if ($message === '') {
            return $this->sendOK([], 'status_changed');
        } else {
            return $this->sendError([], $message, 500);
        }
    }

    public function getSingle(Request $request)
    {
        $record = Sales::selectRaw('Invoice.*,
        Customer.CustomerName as customer,
        Customer.ContactNo1 as ContactNo1,
        Customer.ContactNo2 as ContactNo2,
        Customer.ContactNo3 as ContactNo3,
        PhoneStock.Id as PhoneStockId,
        ManufactureMaster.Name as manufacturer,
        ColorMaster.Name as color,
        ModelMaster.Name as model')
            ->leftJoin('Customer', 'Customer.Id', '=', 'CustomerId')
            ->leftJoin('PhoneStock', 'PhoneStock.IMEI', '=', 'Invoice.IMEI')
            ->leftJoin('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
            ->leftJoin('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
            ->leftJoin('ModelMaster', 'ModelMaster.Id', '=', 'ModelId')
            ->get()
        ;

        if ($record->count()) {
            $response = [];
            $response['record'] = $record->map->transform()->first();
            return $this->sendOK($response);
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }
}
