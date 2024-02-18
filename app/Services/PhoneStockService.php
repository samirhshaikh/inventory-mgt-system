<?php

namespace App\Services;

use App\Exceptions\DuplicateIMEIException;
use App\Exceptions\InvalidIMEIException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\IMEIRequest;
use App\Models\PhoneStock;
use App\Traits\SearchTrait;
use App\Traits\TableActions;
use Illuminate\Database\Eloquent\Builder;

class PhoneStockService
{
    use TableActions, SearchTrait;

    /**
     * @param string $order_by
     * @param string $order_direction
     * @param string $search_type
     * @param string $search_text
     * @param string $search_data
     * @param bool $available_stock_only
     * @return array
     */
    public function getAll(
        string $order_by,
        string $order_direction,
        string $search_type = "simple",
        string $search_text = "",
        string $search_data = "{}",
        bool $available_stock_only = false
    ): array {
        $records = PhoneStock::selectRaw(
            "PhoneStock.*, ManufactureMaster.Name, ColorMaster.Name, ModelMaster.Name"
        )
            ->leftJoin(
                "ManufactureMaster",
                "ManufactureMaster.Id",
                "=",
                "MakeId"
            )
            ->leftJoin("ColorMaster", "ColorMaster.Id", "=", "ColorId")
            ->leftJoin("ModelMaster", "ModelMaster.Id", "=", "ModelId");

        if ($available_stock_only) {
            $records = $records->whereRaw('PhoneStock.Status != "Sold"');
        }

        if ($search_type === "simple" && $search_text != "") {
            $fields_to_search = [
                "IMEI",
                "ManufactureMaster.Name",
                "ColorMaster.Name",
                "ModelMaster.Name",
                "Size",
                "Cost",
                "StockType",
                "ModelNo",
                "Network",
                "Status",
                'DATE_FORMAT(PhoneStock.CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(PhoneStock.UpdatedDate, "%d-%b-%Y")',
            ];

            $records = $this->prepareSearch(
                $records,
                $fields_to_search,
                $search_text,
                "AND"
            );
        } elseif (
            $search_type === "advanced" &&
            $this->searchDataPresent($search_data)
        ) {
            $records = $this->prepareAdvancedSearch(
                $records,
                json_decode($search_data)
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
     * @param $model
     * @param array $search_data
     * @return Builder
     */
    private function prepareAdvancedSearch($model, $search_data = []): Builder
    {
        foreach ($search_data as $column => $search_text) {
            if ($search_text == "" || is_null($search_text)) {
                continue;
            }

            switch ($column) {
                case "make":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "ManufactureMaster.Name",
                        $search_text
                    );
                    break;
                case "model":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "ModelMaster.Name",
                        $search_text
                    );
                    break;
                case "color":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "ColorMaster.Name",
                        $search_text
                    );
                    break;
                case "IMEI":
                case "StockType":
                case "Status":
                case "Size":
                case "Network":
                case "ModelNo":
                case "Cost":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        $column,
                        $search_text
                    );
                    break;
                case "UpdatedDate":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        [
                            'DATE_FORMAT(PhoneStock.CreatedDate, "%d-%b-%Y")',
                            'DATE_FORMAT(PhoneStock.UpdatedDate, "%d-%b-%Y")',
                        ],
                        $search_text,
                        "exact_match"
                    );
                    break;
            }
        }

        return $model;
    }

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingle(IdRequest $request)
    {
        $record = PhoneStock::selectRaw(
            "PhoneStock.*, ManufactureMaster.Name, ColorMaster.Name, ModelMaster.Name"
        )
            ->where("PhoneStock.Id", $request->get("Id"))
            ->leftJoin(
                "ManufactureMaster",
                "ManufactureMaster.Id",
                "=",
                "MakeId"
            )
            ->leftJoin("ColorMaster", "ColorMaster.Id", "=", "ColorId")
            ->leftJoin("ModelMaster", "ModelMaster.Id", "=", "ModelId")
            ->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException();
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
            if ($this->isDuplicateIMEI($row["IMEI"], $row["Id"] ?? 0)) {
                continue;
            }

            //New Record
            if (empty($row["Id"] ?? 0)) {
                $record = new PhoneStock();

                $record->InvoiceId = $invoiceId;
                $record->CreatedBy = session("user_details.UserName");
            }
            //Edit Record
            else {
                $record = PhoneStock::where("Id", $row["Id"])->get();

                if (!$record->count()) {
                    continue;
                }

                $record = $record->first();
            }

            $record->IMEI = $row["IMEI"];
            $record->MakeId = $row["manufacturer"]["Id"];
            $record->ModelId = $row["model"]["Id"];
            $record->ColorId = $row["color"]["Id"];
            $record->Size = $row["Size"];
            $record->Cost = number_format($row["Cost"], 2);
            $record->StockType = $row["StockType"];
            $record->ModelNo = $row["ModelNo"] ?? "";
            $record->Network = $row["Network"];
            $record->Status = $row["Status"];
            $record->UpdatedBy = session("user_details.UserName");
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
        $record = PhoneStock::where("Id", $request->get("Id"));

        if ($record->get()->count()) {
            $record = $record->first();

            $tables_to_check = ["SalesStock"];
            if (
                $this->foreignReferenceFound(
                    $tables_to_check,
                    "IMEI",
                    $record->IMEI
                )
            ) {
                throw new ReferenceException();
            }

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param mixed $imei
     * @param string $status
     * @return bool
     * @throws RecordNotFoundException
     */
    public function changePhoneAvailabilityStatus(
        string $imei,
        string $status = PhoneStock::STATUS_IN_STOCK
    ): bool {
        $record = PhoneStock::where("IMEI", $imei)->get();

        if ($record->count()) {
            $record = $record->first();
            $record->Status = $status;
            $record->UpdatedBy = session("user_details.UserName");
            $record->save();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param IMEIRequest $request
     * @return bool
     * @throws DuplicateIMEIException
     */
    public function checkDuplicateIMEI(IMEIRequest $request): bool
    {
        if (
            $this->isDuplicateIMEI(
                $request->get("IMEI"),
                $request->get("Id", 0)
            )
        ) {
            throw new DuplicateIMEIException();
        } else {
            return false;
        }
    }

    /**
     * @param IMEIRequest $request
     * @return bool
     * @throws InvalidIMEIException
     */
    public function validateIMEI(IMEIRequest $request): bool
    {
        $imei = $request->get("IMEI");

        // Remove any non-numeric characters
        $imei = preg_replace("/[^0-9]/", "", $imei);

        // Check if the IMEI is exactly 15 digits long
        if (strlen($imei) != 15) {
            throw new InvalidIMEIException();
        }

        // Calculate the Luhn check digit
        $sum = 0;
        for ($i = 1; $i <= 15; $i++) {
            $digit = (int) $imei[$i - 1];

            if ($i % 2 == 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
        }

        if ($sum % 10 === 0) {
            return true;
        }

        throw new InvalidIMEIException();
    }

    /**
     * @param mixed $imei
     * @param int $id
     * @return bool
     */
    public function isDuplicateIMEI(mixed $imei, int $id = 0): bool
    {
        //Check whether the record exists or not
        if (!empty($id)) {
            $record = PhoneStock::where("Id", $id)->get();

            if (!$record->count()) {
                return false;
            }
        }

        $record = PhoneStock::whereRaw("LOWER(IMEI) = ?", [strtolower($imei)]);

        if (!empty($id)) {
            $record = $record->where("id", "!=", $id);
        }

        $record = $record->get();

        return $record->count() ? true : false;
    }

    public function getPhonesStatsForStatus()
    {
        $records = PhoneStock::selectRaw(
            "Status, COUNT(*) as stock_total, SUM(Cost) as cost_total"
        )
            ->groupBy("Status")
            ->orderBy("Status")
            ->get();

        return $records->all();
    }
}
