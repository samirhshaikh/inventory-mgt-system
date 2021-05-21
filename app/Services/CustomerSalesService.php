<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveCustomerSalesRequest;
use App\Models\CustomerSales;
use App\Traits\TableActions;

class CustomerSalesService
{
    use TableActions;

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(IdRequest $request)
    {
        $record = CustomerSales::where('Id', $request->get('Id'))
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
            return $this->changeRecordStatus(new CustomerSales, $request);
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param SaveCustomerSalesRequest $request
     * @return int
     * @throws RecordNotFoundException
     */
    public function save(SaveCustomerSalesRequest $request): int
    {
        $record = CustomerSales::where('Id', $request->get('Id'))
            ->get();

        if ($request->get('operation', 'add') == 'edit') {
            if (!$record->count()) {
                throw new RecordNotFoundException;
            }

            $record = $record->first();
        } else {
            $record = new CustomerSales;

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

        return $request->get('operation', 'add') == 'edit'
            ? $request->get('Id')
            : CustomerSales::lastInsertId();
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
        $record = CustomerSales::where('Id', $request->get('Id'));

        if ($record->get()->count()) {
            $tables_to_check = ['Sales'];
            if ($this->foreignReferenceFound($tables_to_check, 'CustomerId', $request->get('Id'))) {
                throw new ReferenceException;
            }

            $record->delete();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }
}
