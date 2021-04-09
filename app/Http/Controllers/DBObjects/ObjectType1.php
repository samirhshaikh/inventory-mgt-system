<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Traits\TableActions;
use Illuminate\Http\Request;

abstract class ObjectType1 extends BaseController
{
    use TableActions;

    abstract protected function getModel();

    abstract protected function getRecordName();

    abstract protected function getColumnIdInReferenceTables();

    public function changeActiveStatus(Request $request)
    {
        $message = $this->changeRecordStatus($this->getModel(), $request);

        if ($message === '') {
            return $this->sendOK([], 'status_changed');
        } else {
            return $this->sendError([], $message, 500);
        }
    }

    public function getSingle(Request $request)
    {
        $model = $this->getModel();
        $record = $model::where('Id', $request->get('Id'))->get();

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
        //Check for duplicate name
        if ($this->isDuplicateName($request->get('Name'), $request->get('Id', 0))) {
            return $this->sendError([], 'duplicate_name', 500);
        }

        $model = $this->getModel();

        $record = $model::where('Id', $request->get('Id'))->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$record->count()) {
                return $this->sendError([], 'record_not_found', 500);
            }

            $record = $record->first();
        } else {
            $record = $this->getModel();

            $record->CreatedBy = session('user_details.UserName');
            $record->IsActive = 1;
        }

        $record->Name = $request->get('Name');
        $record->UpdatedBy = session('user_details.UserName');
        $record->save();

        return $this->sendOK([], 'record_saved');
    }

    public function delete(Request $request)
    {
        $model = $this->getModel();

        //Check whether the record exist or not
        $record = $model::where('Id', $request->get('Id'))->get();

        if ($record->count()) {
            //Check whether the record is used as a reference in other tables.
            $tables_to_check = ['PhoneStock', 'Handsets'];    //'Repair'
            if ($this->foreignReferenceFound($tables_to_check, $this->getColumnIdInReferenceTables(), $request->get('Id'))) {
                return $this->sendError([], 'record_reference_found', 500);
            }

            $model::where('Id', $request->get('Id'))->delete();

            return $this->sendOK([], 'record_deleted');
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function checkDuplicateName(Request $request)
    {
        if ($this->isDuplicateName($request->get('Name'), $request->get('Id', 0))) {
            return $this->sendError([], 'duplicate_name', 500);
        } else {
            return $this->sendOK([]);
        }
    }

    private function isDuplicateName($name, $id = 0)
    {
        $model = $this->getModel();

        //Check whether the record exists or not
        if (!empty($id)) {
            $record = $model::where('Id', $id)->get();

            if (!$record->count()) {
                return false;
            }
        }

        $record = $model::whereRaw('LOWER(Name) = ?', [strtolower($name)]);

        if (!empty($id)) {
            $record = $record->where('id', '!=', $id);
        }

        $record = $record->get();

        return $record->count() ? true : false;
    }
}
