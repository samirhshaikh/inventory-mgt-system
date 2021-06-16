<?php

namespace App\Services;

use App\Exceptions\DuplicateNameException;
use App\Exceptions\NotEnoughRightsException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\SaveUserRequest;
use App\Http\Requests\UserNameRequest;
use App\Models\User;
use App\Traits\TableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use TableActions;

    /**
     * @param Request $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(Request $request)
    {
        $user = User::where('UserName', $request->get('username'))
            ->get();

        if ($user->count()) {
            return $user->map->transform()->first();
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param UserNameRequest $request
     * @return bool
     * @throws NotEnoughRightsException
     * @throws RecordNotFoundException
     */
    public function changeActiveStatus(UserNameRequest $request): bool
    {
        $user = User::where('UserName', $request->get('Id'))
            ->get()
            ->first();

        if ($user) {
            //Only allow to change if the requesting user is an admin
            if (session('user_details.IsAdmin', false)) {
                $user->IsActive = $request->get('value');
                $user->save();

                return true;
            } else {
                throw new NotEnoughRightsException;
            }
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param Request $request
     * @return bool
     * @throws NotEnoughRightsException
     * @throws RecordNotFoundException
     */
    public function changeAdminStatus(Request $request): bool
    {
        $user = User::where('UserName', $request->get('Id'))
            ->get()
            ->first();

        if ($user) {
            //Only allow to change if the requesting user is an admin
            if (session('user_details.IsAdmin', false)) {
                $user->IsAdmin = $request->get('value');
                $user->save();

                return true;
            } else {
                throw new NotEnoughRightsException;
            }
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param SaveUserRequest $request
     * @return string
     * @throws DuplicateNameException
     * @throws RecordNotFoundException
     */
    public function save(SaveUserRequest $request): string
    {
        //Check for duplicate user name
        if ($request->get('operation', 'add') == 'add') {
            if ($this->isDuplicateName($request->get('UserName'))) {
                throw new DuplicateNameException;
            }
        }

        $user = User::where('UserName', $request->get('UserName'))
            ->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$user->count()) {
                throw new RecordNotFoundException;
            }

            $user = $user->first();
        } else {
            $user = new User;

            $user->UserName = $request->get('UserName');
            $user->Password = Hash::make($request->get('Password'));
            $user->CreatedBy = session('user_details.UserName');
        }

        $user->UpdatedBy = session('user_details.UserName');
        $user->IsAdmin = $request->get('IsAdmin');
        $user->IsActive = $request->get('IsActive');
        $user->save();

        return $request->get('UserName');
    }

    /**
     * @param Request $request
     * @return bool
     * @throws RecordNotFoundException
     * @throws ReferenceException
     */
    public function delete(Request $request): bool
    {
        //Check whether the record exist or not
        $user = User::where('UserName', $request->get('username'));

        if ($user->get()->count()) {
            //Check whether the record is used as a reference in other tables.
            $tables_to_check = ['HandsetColors', 'HandsetManufacturers', 'HandsetModels', 'Handsets', 'PhoneStock', 'Suppliers'];
            if ($this->foreignReferenceFound($tables_to_check, ['CreatedBy', 'UpdatedBy'], $request->get('username'))) {
                throw new ReferenceException;
            }

            $user->delete();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param Request $request
     * @return bool
     * @throws DuplicateNameException
     */
    public function checkDuplicateName(Request $request): bool
    {
        if ($this->isDuplicateName($request->get('username'))) {
            throw new DuplicateNameException;
        } else {
            return false;
        }
    }

    /**
     * @param string $user_name
     * @return bool
     */
    private function isDuplicateName(string $user_name): bool
    {
        $user = User::whereRaw('LOWER(UserName) = ?', [strtolower($user_name)])
            ->get();

        return $user->count() ? true : false;
    }
}
