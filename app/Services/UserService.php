<?php

namespace App\Services;

use App\Exceptions\DuplicateNameException;
use App\Exceptions\NotEnoughRightsException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdStringRequest;
use App\Http\Requests\SaveUserRequest;
use App\Http\Requests\UserNameRequest;
use App\Models\User;
use App\Traits\SearchTrait;
use App\Traits\TableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use TableActions, SearchTrait;

    /**
     * @param string $order_by
     * @param string $order_direction
     * @param string $search_text
     * @return array
     */
    public function getAll(
        string $order_by,
        string $order_direction,
        string $search_text = ""
    ): array {
        $records = new User();

        if ($search_text != "") {
            $fields_to_search = [
                "UserName",
                'DATE_FORMAT(CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")',
            ];

            $records = $this->prepareSearch(
                $records,
                $fields_to_search,
                $search_text
            );
        }

        $records = $records->orderBy($order_by, $order_direction);

        //Get total records
        $total_records = $this->getTotalRecords(clone $records);

        return [
            "total_records" => $total_records,
            "records" => $records,
        ];
    }

    /**
     * @param UserNameRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(UserNameRequest $request)
    {
        $user = User::where("UserName", $request->get("username"))->get();

        if ($user->count()) {
            return $user->map->transform()->first();
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param IdStringRequest $request
     * @return bool
     * @throws NotEnoughRightsException
     * @throws RecordNotFoundException
     */
    public function changeActiveStatus(IdStringRequest $request): bool
    {
        $user = User::where("UserName", $request->get("Id"))
            ->get()
            ->first();

        if ($user) {
            //Only allow to change if the requesting user is an admin
            if (session("user_details.IsAdmin", false)) {
                $user->IsActive = $request->get("value");
                $user->save();

                return true;
            } else {
                throw new NotEnoughRightsException();
            }
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param IdStringRequest $request
     * @return bool
     * @throws NotEnoughRightsException
     * @throws RecordNotFoundException
     */
    public function changeAdminStatus(IdStringRequest $request): bool
    {
        $user = User::where("UserName", $request->get("Id"))
            ->get()
            ->first();

        if ($user) {
            //Only allow to change if the requesting user is an admin
            if (session("user_details.IsAdmin", false)) {
                $user->IsAdmin = $request->get("value");
                $user->save();

                return true;
            } else {
                throw new NotEnoughRightsException();
            }
        } else {
            throw new RecordNotFoundException();
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
        if ($request->get("operation", "add") == "add") {
            if ($this->isDuplicateName($request->get("UserName"))) {
                throw new DuplicateNameException();
            }
        }

        $user = User::where("UserName", $request->get("UserName"))->get();

        if ($request->get("operation", "add") == "edit") {
            if (!$user->count()) {
                throw new RecordNotFoundException();
            }

            $user = $user->first();
        } else {
            $user = new User();

            $user->UserName = $request->get("UserName");
            $user->CreatedBy = session("user_details.UserName");
        }

        if (
            $request->get("operation", "add") === "add" ||
            session("user_details.UserName") === $request->get("UserName") ||
            !$request->get("IsAdmin", false)
        ) {
            $user->Password = Hash::make($request->get("Password"));
        }

        $user->UpdatedBy = session("user_details.UserName");
        $user->IsAdmin = $request->get("IsAdmin", false);
        $user->IsActive = $request->get("IsActive", false);
        $user->save();

        return $request->get("UserName");
    }

    /**
     * @param UserNameRequest $request
     * @return bool
     * @throws RecordNotFoundException
     * @throws ReferenceException
     */
    public function delete(UserNameRequest $request): bool
    {
        //Check whether the record exist or not
        $user = User::where("UserName", $request->get("username"));

        if ($user->get()->count()) {
            //Check whether the record is used as a reference in other tables.
            $tables_to_check = [
                "HandsetColors",
                "HandsetManufacturers",
                "HandsetModels",
                "Handsets",
                "PhoneStock",
                "Suppliers",
            ];
            if (
                $this->foreignReferenceFound(
                    $tables_to_check,
                    ["CreatedBy", "UpdatedBy"],
                    $request->get("username")
                )
            ) {
                throw new ReferenceException();
            }

            $user->delete();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param UserNameRequest $request
     * @return bool
     * @throws DuplicateNameException
     */
    public function checkDuplicateName(UserNameRequest $request): bool
    {
        if ($this->isDuplicateName($request->get("username"))) {
            throw new DuplicateNameException();
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
        $user = User::whereRaw("LOWER(UserName) = ?", [
            strtolower($user_name),
        ])->get();

        return $user->count() ? true : false;
    }
}
