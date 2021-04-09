<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Models\Customers;
use App\Traits\TableActions;
use Illuminate\Http\Request;

class CustomersController extends BaseController {
    use TableActions;

    public function changeActiveStatus(Request $request) {
        $message = $this->changeRecordStatus(new Customers, $request);

        if ($message === '') {
            return $this->sendOK([], 'status_changed');
        } else {
            return $this->sendError([], $message, 500);
        }
    }

    public function getSingle(Request $request) {
        $record = Customers::where('Id', $request->get('Id'))
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
        $record = Customers::where('Id', $request->get('Id'))->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$record->count()) {
                return $this->sendError([], 'record_not_found', 500);
            }

            $record = $record->first();
        } else {
            $record = new Customers;

            $record->CreatedBy = session('user_details.UserName');
        }

        $record->CustomerName = $request->get('CustomerName');
        $record->ContactNo1 = $request->get('ContactNo1');
        $record->ContactNo2 = $request->get('ContactNo2');
        $record->Comments = $request->get('Comments');
        $record->Balance = number_format($request->get('Balance'), 2);
        $record->UpdatedBy = session('user_details.UserName');
        $record->IsActive = $request->get('IsActive');
        $record->save();

        return $this->sendOK([], 'record_saved');
    }

    public function delete(Request $request) {
        //Check whether the record exist or not
        $record = Customers::where('Id', $request->get('Id'))->get();

        if ($record->count()) {
            //Check whether the record is used as a reference in other tables.
            $tables_to_check = ['Sales'];//, 'Repair'
            if ($this->foreignReferenceFound($tables_to_check, 'CustomerId', $request->get('Id'))) {
                return $this->sendError([], 'record_reference_found', 500);
            }

            Customers::where('Id', $request->get('Id'))->delete();

            return $this->sendOK([], 'record_deleted');
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }
}
