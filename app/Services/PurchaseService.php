<?php

namespace App\Services;

use App\Exceptions\DuplicateIMEIException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\SavePurchaseRequest;
use App\Http\Requests\IdRequest;
use App\Models\PhoneStock;
use App\Models\Purchase;
use App\Traits\TableActions;
use Carbon\Carbon;

class PurchaseService
{
    use TableActions;

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSinglePurchase(IdRequest $request)
    {
        $record = new Purchase();

        $record = $record->where('Id', $request->get('Id'));

        $record = $record->with('supplier');

        $record = $record->with(['purchases' => function ($query) {
            $query->orderBy('IMEI', 'ASC');
        }]);

        $record = $record->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param SavePurchaseRequest $request
     * @return int
     * @throws DuplicateIMEIException
     * @throws InvalidDataException
     * @throws RecordNotFoundException
     * @throws ReferenceException
     */
    public function save(SavePurchaseRequest $request): int
    {
        /*
         * First we will check for any errors. If yes then return the error code.
         * If no error is found then perform the operation.
         */

        if (count($request->get('children', [])) == 0) {
            throw new InvalidDataException;
        }

        //Check whether the phones marked for deletion are having any reference in other tables or not.
        if (
            $request->get('operation', 'add') == 'edit' && //Delete is only applicable in existing purchases
            count($request->get('children_to_delete', [])) &&
            $this->phoneFoundInForeignTable($request->get('children_to_delete')) //Checking in reference tables
        ) {
            throw new ReferenceException;
        }

        $phonestock_service = new PhoneStockService();

        //Check for duplicate imei
        foreach ($request->get('children', []) as $row) {
            if ($phonestock_service->isDuplicateIMEI($row['IMEI'], $row['Id'] ?? 0)) {
                throw new DuplicateIMEIException;
            }
        }

        //Check whether the purchase being edited exist or not.
        if ($request->get('operation', 'add') == 'edit') {
            $record = Purchase::where('Id', $request->get('Id'))->get();
            if (!$record->count()) {
                throw new RecordNotFoundException;
            }
        }

        //Now delete the child phones
        if (
            $request->get('operation', 'add') == 'edit' &&
            count($request->get('children_to_delete', []))
        ) {
            foreach ($request->get('children_to_delete', []) as $row) {
                PhoneStock::where('Id', $row['Id'])->delete();
            }
        }

        //Create new record in purchase table
        if ($request->get('operation', 'add') == 'edit') {
            $record = $record->first();
        } else {
            $record = new Purchase();

            $record->CreatedBy = session('user_details.UserName');
            $record->IsActive = 1;
        }

        $record->InvoiceNo = $request->get('InvoiceNo');
        $record->InvoiceDate = Carbon::createFromFormat('d-M-Y', $request->get('InvoiceDate'))->toDateTimeString();
        $record->SupplierId = $request->get('SupplierId');
        $record->Comments = $request->get('Comments');
        $record->UpdatedBy = session('user_details.UserName');
        $record->save();

        if ($request->get('operation', 'add') == 'add') {
            $record->Id = Purchase::lastInsertId();
        }

        //Create/Update records in phonestock table
        return $phonestock_service->save($record->Id, $request->get('children', []));
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
        $invoice = Purchase::where('Id', $request->get('Id'))->get();

        if ($invoice->count()) {
            $invoice = $invoice->first();

            //Get all the phones in this invoice
            $phones = PhoneStock::where('InvoiceId', $invoice->Id)->get();
            if ($phones->count()) {
                $tables_to_check = ['SalesStock'];
                foreach ($phones as $phone) {
                    if ($this->foreignReferenceFound($tables_to_check, 'IMEI', $phone->IMEI)) {
                        throw new ReferenceException;
                    }
                }

                $tables_to_check = ['TradedDetails'];
                foreach ($phones as $phone) {
                    if ($this->foreignReferenceFound($tables_to_check, 'PhoneStockId', $phone->Id)) {
                        throw new ReferenceException;
                    }
                }

                foreach ($phones as $phone) {
                    PhoneStock::where('Id', $phone->Id)->delete();
                }
            }

            Purchase::where('Id', $request->get('Id'))->delete();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param array $phone_ids
     * @return bool
     */
    protected function phoneFoundInForeignTable(array $phone_ids): bool
    {
        $tables_to_check = ['SalesStock'];
        foreach ($phone_ids as $phone_id) {
            if ($this->foreignReferenceFound($tables_to_check, 'IMEI', $phone_id)) {
                return true;
            }
        }

        $tables_to_check = ['TradedDetails'];
        foreach ($phone_ids as $phone_id) {
            if ($this->foreignReferenceFound($tables_to_check, 'PhoneStockId', $phone_id)) {
                return true;
            }
        }

        return false;
    }
}
