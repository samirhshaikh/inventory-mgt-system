<?php

namespace App\Services;

use App\Exceptions\DuplicateIMEIException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\IMEIRequest;
use App\Models\PhoneStock;
use App\Traits\TableActions;

class PhoneStockService
{
    use TableActions;

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(IdRequest $request)
    {
        $record = PhoneStock::selectRaw('PhoneStock.*, Supplier.SupplierName as supplier, ManufactureMaster.Name as manufacturer, ColorMaster.Name as color, modelmaster.Name as model')
            ->where('PhoneStock.Id', $request->get('Id'))
            ->join('Supplier', 'Supplier.Id', '=', 'SupplierId')
            ->join('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
            ->join('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
            ->join('modelmaster', 'modelmaster.Id', '=', 'ModelId')
            ->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param $invoiceId
     * @param array $phones
     * @return int
     */
    public function save($invoiceId, array $phones): int
    {
        $records_count = 0;
        foreach ($phones as $row) {
            if ($this->isDuplicateIMEI($row['IMEI'], $row['Id'] ?? 0)) {
                continue;
            }

            //New Record
            if (empty($row['Id'] ?? 0)) {
                $record = new PhoneStock;

                $record->InvoiceId = $invoiceId;
                $record->CreatedBy = session('user_details.UserName');
            } //Edit Record
            else {
                $record = PhoneStock::where('Id', $row['Id'])->get();

                if (!$record->count()) {
                    continue;
                }

                $record = $record->first();
            }

            $record->IMEI = $row['IMEI'];
            $record->MakeId = $row['manufacturer']['Id'];
            $record->ModelId = $row['model']['Id'];
            $record->ColorId = $row['color']['Id'];
            $record->Size = $row['Size'];
            $record->Cost = number_format($row['Cost'], 2);
            $record->StockType = $row['StockType'];
            $record->ModelNo = $row['ModelNo'] ?? '';
            $record->Network = $row['Network'];
            $record->Status = $row['Status'];
            $record->UpdatedBy = session('user_details.UserName');
            $record->IsActive = 1;
            $record->save();

            $records_count++;
        }

        return $records_count;
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
        $record = PhoneStock::where('Id', $request->get('Id'));

        if ($record->get()->count()) {
            $record = $record->first();

            $tables_to_check = ['Sales'];
            if ($this->foreignReferenceFound($tables_to_check, 'IMEI', $record->IMEI)) {
                throw new ReferenceException;
            }

            $tables_to_check = ['TradedDetails'];
            if ($this->foreignReferenceFound($tables_to_check, 'PhoneStockId', $request->get('Id'))) {
                throw new ReferenceException;
            }

            $record->delete();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param mixed $imei
     * @param string $status
     * @return bool
     * @throws RecordNotFoundException
     */
    public function changePhoneAvailabilityStatus($imei, $status = PhoneStock::STATUS_IN_STOCK): bool
    {
        $record = PhoneStock::where('IMEI', $imei)
            ->get();

        if ($record->count()) {
            $record = $record->first();
            $record->Status = $status;
            $record->UpdatedBy = session('user_details.UserName');
            $record->save();

            return true;
        } else {
            throw new RecordNotFoundException;
        }
    }

    /**
     * @param IMEIRequest $request
     * @return bool
     * @throws DuplicateIMEIException
     */
    public function checkDuplicateIMEI(IMEIRequest $request): bool
    {
        if ($this->isDuplicateIMEI($request->get('IMEI'), $request->get('Id', 0))) {
            throw new DuplicateIMEIException;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $imei
     * @param $id
     * @return bool
     */
    public function isDuplicateIMEI($imei, $id = 0): bool
    {
        //Check whether the record exists or not
        if (!empty($id)) {
            $record = PhoneStock::where('Id', $id)->get();

            if (!$record->count()) {
                return false;
            }
        }

        $record = PhoneStock::whereRaw('LOWER(IMEI) = ?', [strtolower($imei)]);

        if (!empty($id)) {
            $record = $record->where('id', '!=', $id);
        }

        $record = $record->get();

        return $record->count() ? true : false;
    }
}
