<?php

namespace App\Services;

use App\Exceptions\DuplicateNameException;
use App\Exceptions\RecordNotFoundException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveHandsetRequest;
use App\Models\Handsets;
use App\Traits\TableActions;
use Illuminate\Http\Request;

class HandsetsService
{
    use TableActions;

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(IdRequest $request)
    {
        $record = Handsets::selectRaw(
            "HandsetMaster.*, ManufactureMaster.Name as manufacturer, ColorMaster.Name as color, modelmaster.Name as model"
        )
            ->where("HandsetMaster.id", $request->get("id"))
            ->join("ManufactureMaster", "ManufactureMaster.id", "=", "MakeId")
            ->join("ColorMaster", "ColorMaster.id", "=", "ColorId")
            ->join("modelmaster", "modelmaster.id", "=", "ModelId")
            ->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    public function changeActiveStatus(IdRequest $request): bool
    {
        try {
            return $this->changeRecordStatus(new Handsets(), $request);
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param SaveHandsetRequest $request
     * @return int
     * @throws DuplicateNameException
     * @throws RecordNotFoundException
     */
    public function save(SaveHandsetRequest $request): int
    {
        //Check for duplicate name
        if (
            $this->isDuplicateName(
                $request->get("Name"),
                $request->get("id", 0)
            )
        ) {
            throw new DuplicateNameException();
        }

        $record = Handsets::where("id", $request->get("id"))->get();

        if ($request->get("operation", "add") == "edit") {
            if (!$record->count()) {
                throw new RecordNotFoundException();
            }

            $record = $record->first();
        } else {
            $record = new Handsets();

            $record->CreatedBy = session("user_details.UserName");
        }

        $record->Name = $request->get("Name");
        $record->ColorId = $request->get("ColorId");
        $record->MakeId = $request->get("MakeId");
        $record->ModelId = $request->get("ModelId");
        $record->UpdatedBy = session("user_details.UserName");
        $record->IsActive = $request->get("IsActive");
        $record->save();

        return $request->get("operation", "add") == "edit"
            ? $request->get("id")
            : Handsets::lastInsertId();
    }

    /**
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    public function delete(IdRequest $request): bool
    {
        //Check whether the record exist or not
        $record = Handsets::where("id", $request->get("id"));

        if ($record->get()->count()) {
            //ToDo: Find the references of Handsets models before deleting.
            //            $tables_to_check = ['Purchase'];
            //            if ($this->foreignReferenceFound($tables_to_check, 'SupplierId', $request->get('id'))) {
            //                throw new ReferenceException;
            //            }

            $record->delete();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param Request $request
     * @return bool
     * @throws DuplicateNameException
     */
    public function checkDuplicateName(Request $request): bool
    {
        if (
            $this->isDuplicateName(
                $request->get("Name"),
                $request->get("id", 0)
            )
        ) {
            throw new DuplicateNameException();
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     * @param int $id
     * @return bool
     */
    private function isDuplicateName(string $name, int $id = 0): bool
    {
        //Check whether the record exists or not
        if (!empty($id)) {
            $record = Handsets::where("id", $id)->get();

            if (!$record->count()) {
                return false;
            }
        }

        $record = Handsets::whereRaw("LOWER(Name) = ?", [strtolower($name)]);

        if (!empty($id)) {
            $record = $record->where("id", "!=", $id);
        }

        $record = $record->get();

        return $record->count() ? true : false;
    }
}
