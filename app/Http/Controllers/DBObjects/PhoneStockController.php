<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Models\PhoneStock;
use App\Traits\TableActions;
use Illuminate\Http\Request;

class PhoneStockController extends BaseController
{
    use TableActions;

    public function changeActiveStatus(Request $request)
    {
        $message = $this->changeRecordStatus(new PhoneStock, $request);

        if ($message === '') {
            return $this->sendOK([], 'status_changed');
        } else {
            return $this->sendError([], $message, 500);
        }
    }

    public function getSingle(Request $request)
    {
        $record = PhoneStock::selectRaw('PhoneStock.*, Supplier.SupplierName as supplier, ManufactureMaster.Name as manufacturer, ColorMaster.Name as color, modelmaster.Name as model')
            ->where('PhoneStock.Id', $request->get('Id'))
            ->join('Supplier', 'Supplier.Id', '=', 'SupplierId')
            ->join('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
            ->join('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
            ->join('modelmaster', 'modelmaster.Id', '=', 'ModelId')
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

    public static function save($invoiceId, Array $phones)
    {
        $records_count = 0;
        foreach ($phones as $row) {
            if (self::isDuplicateIMEI($row['IMEI'], $row['Id'] ?? 0)) {
                continue;
            }

            //New Record
            if (empty($row['Id'] ?? 0)) {
                $record = new PhoneStock;

                $record->InvoiceId = $invoiceId;
                $record->CreatedBy = session('user_details.UserName');
            }
            //Edit Record
            else {
                $record = PhoneStock::where('Id', $row['Id'])->get();

                if (!$record->count()) {
                    continue;
                }

                $record = $record->first();
            }

            $record->IMEI = $row['IMEI'];
            $record->MakeId = $row['manufacturer']['Id'];
            $record->ModelId = $row['model']['Id'];
            $record->ColorId = $row['color']['Id'];
            $record->Size = $row['Size'];
            $record->Cost = number_format($row['Cost'], 2);
            $record->StockType = $row['StockType'];
            $record->ModelNo = $row['ModelNo'] ?? '';
            $record->Network = $row['Network'];
            $record->Status = $row['Status'];
            $record->UpdatedBy = session('user_details.UserName');
            $record->IsActive = 1;
            $record->save();

            $records_count++;
        }

        return $records_count;
    }

    public function delete(Request $request)
    {
        //Check whether the record exist or not
        $record = PhoneStock::where('Id', $request->get('Id'))->get();

        if ($record->count()) {
            $record = $record->first();

            $tables_to_check = ['Sales'];
            if ($this->foreignReferenceFound($tables_to_check, 'IMEI', $record->IMEI)) {
                return $this->sendError([], 'record_reference_found', 500);
            }

            $tables_to_check = ['TradedDetails'];
            if ($this->foreignReferenceFound($tables_to_check, 'PhoneStockId', $request->get('Id'))) {
                return $this->sendError([], 'record_reference_found', 500);
            }

            PhoneStock::where('Id', $request->get('Id'))->delete();

            return $this->sendOK([], 'record_deleted');
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function checkDuplicateIMEI(Request $request)
    {
        if ($this->isDuplicateIMEI($request->get('IMEI'), $request->get('Id', 0))) {
            return $this->sendError([], 'duplicate_imei', 500);
        } else {
            return $this->sendOK([]);
        }
    }

    public static function isDuplicateIMEI($imei, $id = 0)
    {
        //Check whether the record exists or not
        if (!empty($id)) {
            $record = PhoneStock::where('Id', $id)->get();

            if (!$record->count()) {
                return false;
            }
        }

        $record = PhoneStock::whereRaw('LOWER(IMEI) = ?', [strtolower($imei)]);

        if (!empty($id)) {
            $record = $record->where('id', '!=', $id);
        }

        $record = $record->get();

        return $record->count() ? true : false;
    }
}
