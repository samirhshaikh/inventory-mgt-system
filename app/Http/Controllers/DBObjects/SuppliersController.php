<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Models\Suppliers;
use App\Traits\TableActions;
use Illuminate\Http\Request;

class SuppliersController extends BaseController {
    use TableActions;

    public function changeActiveStatus(Request $request) {
        $message = $this->changeRecordStatus(new Suppliers, $request);

        if ($message === '') {
            return $this->sendOK([], 'status_changed');
        } else {
            return $this->sendError([], $message, 500);
        }
    }

    public function getSingle(Request $request) {
        $record = Suppliers::where('Id', $request->get('Id'))
            ->get();

        if ($record->count()) {
            $response = [];
            $response['record'] = $record->map->transform()->first();
            return $this->sendOK($response);
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function save(Request $request) {
        $record = Suppliers::where('Id', $request->get('Id'))->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$record->count()) {
                return $this->sendError([], 'record_not_found', 500);
            }

            $record = $record->first();
        } else {
            $record = new Suppliers;

            $record->CreatedBy = session('user_details.UserName');
        }

        $record->SupplierName = $request->get('SupplierName');
        $record->ContactNo1 = $request->get('ContactNo1');
        $record->ContactNo2 = $request->get('ContactNo2');
        $record->CurrentBalance = number_format($request->get('CurrentBalance'), 2);
        $record->Comments = $request->get('Comments');
        $record->UpdatedBy = session('user_details.UserName');
        $record->IsActive = $request->get('IsActive');
        $record->save();

        return $this->sendOK([], 'record_saved');
    }

    public function delete(Request $request) {
        //Check whether the record exist or not
        $record = Suppliers::where('Id', $request->get('Id'))->get();

        if ($record->count()) {
            $tables_to_check = ['PhoneStock'];
            if ($this->foreignReferenceFound($tables_to_check, 'SupplierId', $request->get('Id'))) {
                return $this->sendError([], 'record_reference_found', 500);
            }

            Suppliers::where('Id', $request->get('Id'))->delete();

            return $this->sendOK([], 'record_deleted');
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }
}
