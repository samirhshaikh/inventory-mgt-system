<?php

namespace App\Http\Controllers\DBObjects;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Traits\TableActions;
use Illuminate\Http\Request;

class UsersController extends BaseController
{
    use TableActions;

    public function changeActiveStatus(Request $request)
    {
        $user = User::where('UserName', $request->get('Id'))
            ->first();

        if ($user) {
            //Only allow to change if the requesting user is an admin
            if (session('user_details.IsAdmin', false)) {
                $user->IsActive = $request->get('value');
                $user->save();

                return $this->sendOK([], 'status_changed');
            } else {
                return $this->sendError([], 'not_enough_rights', 500);
            }
        } else {
            return $this->sendError([], 'User not found.', 500);
        }
    }

    public function changeAdminStatus(Request $request)
    {
        $user = User::where('UserName', $request->get('Id'))
            ->first();

        if ($user) {
            //Only allow to change if the requesting user is an admin
            if (session('user_details.IsAdmin', false)) {
                $user->IsAdmin = $request->get('value');
                $user->save();

                return $this->sendOK([], 'status_changed');
            } else {
                return $this->sendError([], 'not_enough_rights', 500);
            }
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function getSingle(Request $request)
    {
        $user = User::where('UserName', $request->get('user_name'))->get();

        if ($user->count()) {
            $response = [];
            $response['record'] = $user->map->transform()->first();
            return $this->sendOK($response);
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function save(Request $request)
    {
        //Check for duplicate user name
        if ($request->get('operation', 'add') == 'add') {
            if ($this->isDuplicateName($request->get('UserName'))) {
                return $this->sendError([], 'duplicate_name', 500);
            }
        }

        $user = User::where('UserName', $request->get('UserName'))->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$user->count()) {
                return $this->sendError([], 'record_not_found', 500);
            }

            $user = $user->first();
        } else {
            $user = new User;

            $user->UserName = $request->get('UserName');
            $user->Password = $request->get('Password');
            $user->CreatedBy = session('user_details.UserName');
        }

        $user->UpdatedBy = session('user_details.UserName');
        $user->IsAdmin = $request->get('IsAdmin');
        $user->IsActive = $request->get('IsActive');
        $user->save();

        return $this->sendOK([], 'record_saved');
    }

    public function delete(Request $request)
    {
        //Check whether the record exist or not
        $user = User::where('UserName', $request->get('UserName'))->get();

        if ($user->count()) {
            //Check whether the record is used as a reference in other tables.
            $tables_to_check = ['HandsetColors', 'HandsetManufacturers', 'HandsetModels', 'Handsets', 'PhoneStock', 'Suppliers'];
            if ($this->foreignReferenceFound($tables_to_check, ['CreatedBy', 'UpdatedBy'], $request->get('UserName'))) {
                return $this->sendError([], 'record_reference_found', 500);
            }

            User::where('UserName', $request->get('UserName'))->delete();

            return $this->sendOK([], 'record_deleted');
        } else {
            return $this->sendError([], 'record_not_found', 500);
        }
    }

    public function checkDuplicateName(Request $request)
    {
        if ($this->isDuplicateName($request->get('UserName'))) {
            return $this->sendError([], 'duplicate_name', 500);
        } else {
            return $this->sendOK([]);
        }
    }

    private function isDuplicateName($userName)
    {
        $user = User::whereRaw('LOWER(UserName) = ?', [strtolower($userName)]);

        $user = $user->get();

        return $user->count() ? true : false;
    }
}
