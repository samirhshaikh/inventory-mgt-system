<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Models\Handsets;
use Illuminate\Http\Request;

class HandsetsController extends BaseController
{
    public function changeActiveStatus(Request $request)
    {
        $message = $this->changeRecordStatus(new Handsets, $request);

        if ($message === '') {
            return $this->sendOK([], 'status_changed');
        } else {
            return $this->sendError([], $message, 500);
        }
    }

    public function getSingle(Request $request)
    {
        $record = Handsets::selectRaw('HandsetMaster.*, ManufactureMaster.Name as manufacturer, ColorMaster.Name as color, modelmaster.Name as model')
            ->where('HandsetMaster.Id', $request->get('Id'))
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

    public function save(Request $request)
    {
        //Check for duplicate name
        if ($this->isDuplicateName($request->get('Name'), $request->get('Id', 0))) {
            return $this->sendError([], 'duplicate_name', 500);
        }

        $record = Handsets::where('Id', $request->get('Id'))->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$record->count()) {
                return $this->sendError([], 'record_not_found', 500);
            }

            $record = $record->first();
        } else {
            $record = new Handsets;

            $record->CreatedBy = session('user_details.UserName');
        }

        $record->Name = $request->get('Name');
        $record->ColorId = $request->get('ColorId');
        $record->MakeId = $request->get('MakeId');
        $record->ModelId = $request->get('ModelId');
        $record->UpdatedBy = session('user_details.UserName');
        $record->IsActive = $request->get('IsActive');
        $record->save();

        return $this->sendOK([], 'record_saved');
    }

    public function delete(Request $request)
    {
        $record = Handsets::where('Id', $request->get('Id'))->get();

        if ($record->count()) {
            Handsets::where('Id', $request->get('Id'))->delete();

            //Remove the reference of user from all the tables.

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
        //Check whether the record exists or not
        if (!empty($id)) {
            $record = Handsets::where('Id', $id)->get();

            if (!$record->count()) {
                return false;
            }
        }

        $record = Handsets::whereRaw('LOWER(Name) = ?', [strtolower($name)]);

        if (!empty($id)) {
            $record = $record->where('id', '!=', $id);
        }

        $record = $record->get();

        return $record->count() ? true : false;
    }
}
