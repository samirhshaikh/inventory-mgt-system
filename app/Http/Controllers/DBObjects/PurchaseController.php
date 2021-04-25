<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Models\PhoneStock;
use App\Models\Purchase;
use App\Traits\TableActions;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseController extends BaseController
{
    use TableActions;

    public function getSingle(Request $request)
    {
        $record = new Purchase();

        $record = $record->where('Id', $request->get('Id'));

        $record = $record->with('supplier');

        $record = $record->with(['purchases' => function ($query) {
            $query->orderBy('IMEI', 'ASC');
        }]);

        $record = $record->get();

        if ($record->count()) {
            $response = [];
            $response['record'] = $record->map->transform()->first();
            return $this->sendOK($response);
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function save(Request $request)
    {
        /*
         * First we will check for any errors. If yes then return the error code.
         * If no error is found then perform the operation.
         */

        if (count($request->get('childs', [])) == 0) {
            return $this->sendError([], 'invalid_data', 500);
        }

        //Check whether the phones marked for deletion are having any reference in other tables or not.
        if (
            $request->get('operation', 'add') == 'edit' && //Delete is only applicable in existing purchases
            count($request->get('deleted_childs', [])) &&
            $this->phoneFoundInForeignTable($request->get('deleted_childs')) //Checking in reference tables
        ) {
            return $this->sendError([], 'record_reference_found', 500);
        }

        //Check for duplicate imei
        foreach ($request->get('childs', []) as $row) {
            if (PhoneStockController::isDuplicateIMEI($row['IMEI'], $row['Id'] ?? 0)) {
                return $this->sendError([], 'duplicate_imei', 500);
            }
        }

        //Check whether the purchase being edited exist or not.
        if ($request->get('operation', 'add') == 'edit') {
            $record = Purchase::where('Id', $request->get('Id'))->get();
            if (!$record->count()) {
                return $this->sendError([], 'record_not_found', 500);
            }
        }

        //Now delete the child phones
        if (
            $request->get('operation', 'add') == 'edit' &&
            count($request->get('deleted_childs', []))
        ) {
            foreach ($request->get('deleted_childs', []) as $row) {
                PhoneStock::where('Id', $row['Id'])->delete();
            }
        }

        //Create new record in purchase table
        if ($request->get('operation', 'add') == 'edit') {
            $record = $record->first();
        } else {
            $record = new Purchase();

            $record->CreatedBy = session('user_details.UserName');
            $record->IsActive = 1;
        }

        $record->InvoiceNo = $request->get('InvoiceNo');
        $record->InvoiceDate = Carbon::createFromFormat('d-M-Y', $request->get('InvoiceDate'))->toDateTimeString();
        $record->SupplierId = $request->get('SupplierId');
        $record->Comments = $request->get('Comments');
        $record->UpdatedBy = session('user_details.UserName');
        $record->save();

        if ($request->get('operation', 'add') == 'add') {
            $record->Id = Purchase::lastInsertId();
        }

        //Create/Update records in phonestock table
        $records_count = PhoneStockController::save($record->Id, $request->get('childs', []));

        return $this->sendOK(['records_count' => $records_count], 'record_saved');
    }

    public function delete(Request $request)
    {
        //Check whether the record exist or not
        $invoice = Purchase::where('Id', $request->get('Id'))->get();

        if ($invoice->count()) {
            $invoice = $invoice->first();

            //Get all the phones in this invoice
            $phones = PhoneStock::where('InvoiceId', $invoice->Id)->get();
            if ($phones->count()) {
                $tables_to_check = ['Sales'];
                foreach ($phones as $phone) {
                    if ($this->foreignReferenceFound($tables_to_check, 'IMEI', $phone->IMEI)) {
                        return $this->sendError([], 'record_reference_found', 500);
                    }
                }

                $tables_to_check = ['TradedDetails'];
                if ($this->foreignReferenceFound($tables_to_check, 'PhoneStockId', $phone->Id)) {
                    return $this->sendError([], 'record_reference_found', 500);
                }

                foreach ($phones as $phone) {
                    PhoneStock::where('Id', $phone->Id)->delete();
                }
            }

            Purchase::where('Id', $request->get('Id'))->delete();

            return $this->sendOK([], 'record_deleted');
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    protected function phoneFoundInForeignTable($phone_ids)
    {
        $tables_to_check = ['Sales'];
        foreach ($phone_ids as $phone_id) {
            if ($this->foreignReferenceFound($tables_to_check, 'IMEI', $phone_id)) {
                return true;
            }
        }

        $tables_to_check = ['TradedDetails'];
        foreach ($phone_ids as $phone_id) {
            if ($this->foreignReferenceFound($tables_to_check, 'PhoneStockId', $phone_id)) {
                return true;
            }
        }

        return false;
    }
}
