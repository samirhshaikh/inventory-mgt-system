<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveSupplierRequest;
use App\Models\Suppliers;
use App\Traits\TableActions;

class SuppliersService
{
    use TableActions;

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(IdRequest $request)
    {
        $record = Suppliers::where('Id', $request->get('Id'))
            ->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException;
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
            return $this->changeRecordStatus(new Suppliers, $request);
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param SaveSupplierRequest $request
     * @return int
     * @throws RecordNotFoundException
     */
    public function save(SaveSupplierRequest $request): int
    {
        $record = Suppliers::where('Id', $request->get('Id'))
            ->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$record->count()) {
                throw new RecordNotFoundException;
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

        return $request->get('operation', 'add') == 'edit'
            ? $request->get('Id')
            : Suppliers::lastInsertId();
    }

    /**
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     * @throws ReferenceException
     */
    public function delete(IdRequest $request): bool
    {
        //Check whether the record exist or not
        $record = Suppliers::where('Id', $request->get('Id'));

        if ($record->get()->count()) {
            $tables_to_check = ['Purchase'];
            if ($this->foreignReferenceFound($tables_to_check, 'SupplierId', $request->get('Id'))) {
                throw new ReferenceException;
            }

            $record->delete();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }
}
