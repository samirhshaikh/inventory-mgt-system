<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveRepairRequest;
use App\Models\PhoneStock;
use App\Models\Purchase;
use App\Models\Repair;
use App\Models\RepairPart;
use App\Traits\SearchTrait;
use App\Traits\TableActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RepairsService
{
    use TableActions, SearchTrait;

    /**
     * @param string $order_by
     * @param string $order_direction
     * @param string $search_type
     * @param string $search_text
     * @param string $search_data
     * @return array
     */
    public function getAll(
        string $order_by,
        string $order_direction,
        string $search_type = "simple",
        string $search_text = "",
        string $search_data = "{}"
    ): array {
        //        DB::enableQueryLog();

        $invoice_ids = null;
        if (
            ($search_type === "simple" && $search_text != "") ||
            ($search_type === "advanced" &&
                $this->searchDataPresent($search_data))
        ) {
            $invoice_ids = $this->getInvoiceIds(
                $search_type,
                $search_text,
                $search_data
            );
        }

        $records = new Repair();

        $records = $records
            ->with("customer")
            ->with("manufacturer")
            ->with("model")
            ->with("color");

        if (!is_null($invoice_ids)) {
            $records = $records->whereIn("Repairs.id", $invoice_ids);
        }

        //Get total records
        $total_records = $this->getTotalRecords(clone $records);

        $records = $records->with([
            "parts" => function ($query) {
                $query->orderBy("PartId", "ASC");
            },
        ]);

        switch ($order_by) {
            case "children":
                $records = $records
                    ->leftJoin(
                        "Repairs_Parts",
                        "Repairs_Parts.RepairId",
                        "=",
                        "Repairs.id"
                    )
                    ->groupBy("Repairs.id")
                    ->orderBy("PartId", $order_direction);
                break;
            case "customer.CustomerName":
                $records = $records->leftJoin(
                    "customers",
                    "customers.id",
                    "=",
                    "CustomerId"
                );
                break;
            case "UpdatedDate":
                $records = $records->orderBy(
                    "Repairs.UpdatedDate",
                    $order_direction
                );
                break;
            default:
                $records = $records->orderBy($order_by, $order_direction);
        }

        $records = $records->addSelect("Repairs.*");

        return [
            "total_records" => $total_records,
            "records" => $records,
        ];
    }

    /**
     * @param string $search_type
     * @param string $search_text
     * @param string $search_data
     * @return array
     */
    private function getInvoiceIds(
        string $search_type,
        string $search_text = "",
        string $search_data = "{}"
    ): array {
        try {
            $records = Repair::selectRaw("Repairs.id");

            if (
                ($search_type === "simple" && $search_text != "") ||
                ($search_type === "advanced" &&
                    $this->searchDataPresent($search_data))
            ) {
                $records = $records
                    ->leftJoin("customers", "customers.id", "=", "CustomerId")
                    ->join(
                        "Repairs_Parts",
                        "Repairs_Parts.RepairId",
                        "=",
                        "Repairs.id"
                    )
                    ->join(
                        "ManufactureMaster",
                        "ManufactureMaster.id",
                        "=",
                        "MakeId"
                    )
                    ->join("ColorMaster", "ColorMaster.id", "=", "ColorId")
                    ->join("ModelMaster", "ModelMaster.id", "=", "ModelId");
            }

            if ($search_type === "simple" && $search_text != "") {
                $fields_to_search = [
                    $this->getInvoiceSearchString(),
                    'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                    'DATE_FORMAT(ReceivedDate, "%d-%b-%Y")',
                    "customers.CustomerName",
                    "customers.ContactNo1",
                    "customers.ContactNo2",
                    "Repairs_Parts.IMEI",
                    "ManufactureMaster.Name",
                    "ColorMaster.Name",
                    "ModelMaster.Name",
                    "Repairs.Amount",
                    "Repairs.Notes",
                    "Repairs.ReasonForNotRepair",
                    'DATE_FORMAT(Repairs.CreatedDate, "%d-%b-%Y")',
                    'DATE_FORMAT(Repairs.UpdatedDate, "%d-%b-%Y")',
                ];

                $records = $this->prepareSearch(
                    $records,
                    $fields_to_search,
                    $search_text
                );
            } elseif (
                $search_type === "advanced" &&
                $this->searchDataPresent($search_data)
            ) {
                $records = $this->prepareAdvancedSearch(
                    $records,
                    json_decode($search_data)
                );

                //            dd($this->getSql($records));
            }

            $records = $records->orderBy("Repairs.id", "ASC")->get();

            return $records->pluck("id")->all();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param $model
     * @param mixed $search_data
     * @return Builder
     */
    private function prepareAdvancedSearch(
        $model,
        mixed $search_data = []
    ): Builder {
        foreach ($search_data as $column => $search_text) {
            if ($search_text == "" || is_null($search_text)) {
                continue;
            }

            switch ($column) {
                case "customer":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        [
                            "customers.CustomerName",
                            "customers.ContactNo1",
                            "customers.ContactNo2",
                        ],
                        $search_text
                    );
                    break;
                case "contact":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        ["customers.ContactNo1", "customers.ContactNo2"],
                        $search_text
                    );
                    break;
                case "InvoiceDate":
                    list($start_date, $end_date) = explode(",", $search_text);
                    if ($end_date) {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            "Repairs.InvoiceDate",
                            [
                                DateService::convertToMySQL($start_date),
                                DateService::convertToMySQL($end_date),
                            ],
                            "date_range"
                        );
                    } else {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            'DATE_FORMAT(Repairs.InvoiceDate, "%d-%b-%Y")',
                            $start_date,
                            "exact_match"
                        );
                    }

                    break;
                case "ReceivedDate":
                    list($start_date, $end_date) = explode(",", $search_text);
                    if ($end_date) {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            "Repairs.ReceivedDate",
                            [
                                DateService::convertToMySQL($start_date),
                                DateService::convertToMySQL($end_date),
                            ],
                            "date_range"
                        );
                    } else {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            'DATE_FORMAT(Repairs.ReceivedDate, "%d-%b-%Y")',
                            $start_date,
                            "exact_match"
                        );
                    }

                    break;
                case "manufacturer":
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
                case "InvoiceNo":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        ["InvoiceNo", $this->getInvoiceSearchString()],
                        $search_text
                    );
                    break;
                case "IMEI":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "Repairs.IMEI",
                        $search_text
                    );
                    break;
                case "Amount":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "Repairs." . $column,
                        $search_text
                    );
                    break;
                case "PaymentMethod":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "Repairs." . $column,
                        $search_text,
                        "exact_match"
                    );
                    break;
                case "ReasonForNotRepair":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "Repairs_Parts." . $column,
                        $search_text
                    );
                    break;
                case "UpdatedDate":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        [
                            'DATE_FORMAT(Repairs.CreatedDate, "%d-%b-%Y")',
                            'DATE_FORMAT(Repairs.UpdatedDate, "%d-%b-%Y")',
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
    public function getSingleRepair(IdRequest $request): mixed
    {
        $record = new Repair();

        $record = $record->where("id", $request->get("id"));

        $record = $record
            ->with("customer")
            ->with("manufacturer")
            ->with("model")
            ->with("color");

        $record = $record->with([
            "parts" => function ($query) {
                $query->orderBy("PartId", "ASC");
            },
        ]);

        $record = $record->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException();
        }
    }

    public function save(SaveRepairRequest $request): array
    {
        //Check whether the repair being edited exist or not.
        if ($request->get("operation", "add") == "edit") {
            $record = Repair::where("id", $request->get("id"))->get();
            if (!$record->count()) {
                throw new RecordNotFoundException();
            }
        }

        //Now delete the child repair parts
        if (
            $request->get("operation", "add") == "edit" &&
            count($request->get("children_to_delete", []))
        ) {
            foreach ($request->get("children_to_delete", []) as $row) {
                RepairPart::where("id", $row["id"])->delete();
            }
        }

        //Create new record in purchase table
        if ($request->get("operation", "add") == "edit") {
            $record = $record->first();
        } else {
            $record = new Repair();

            $record->InvoiceNo = $this->getNextInvoiceNo();
            $record->CreatedBy = session("user_details.UserName");
        }

        $record->InvoiceDate = Carbon::createFromFormat(
            "d-M-Y",
            $request->get("InvoiceDate")
        )->toDateTimeString();
        $record->ReceivedDate = Carbon::createFromFormat(
            "d-M-Y",
            $request->get("ReceivedDate")
        )->toDateTimeString();
        $record->CustomerId = $request->get("CustomerId");
        $record->PaymentMethod = $request->get("PaymentMethod");
        $record->Amount = $request->get("Amount", 0);
        $record->VAT = $request->get("VAT", 0);
        $record->IMEI = $request->get("IMEI");
        $record->MakeId = $request->get("manufacturer")["id"];
        $record->ModelId = $request->get("model")["id"];
        $record->ColorId = $request->get("color")["id"];
        $record->Notes = $request->get("Notes");
        $record->ReasonForNotRepair = $request->get("ReasonForNotRepair");
        $record->UpdatedBy = session("user_details.UserName");
        $record->save();

        if ($request->get("operation", "add") == "add") {
            $record->id = Repair::lastInsertId();
        }

        $repair_part_service = new RepairPartService();

        //Create/Update records in repair part table
        $records_count = $repair_part_service->save(
            $record->id,
            $request->get("children", [])
        );

        return [
            "id" => $record->id,
            "records_count" => $records_count,
        ];
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
        $repair = Repair::where("id", $request->get("id"))->get();

        if ($repair->count()) {
            $repair = $repair->first();

            //Get all the phones in this invoice
            $parts = RepairPart::where("RepairId", $repair->id)->get();
            if ($parts->count()) {
                foreach ($parts as $part) {
                    RepairPart::where("id", $part->id)->delete();
                }
            }

            Repair::where("id", $request->get("id"))->delete();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @return int
     */
    public function getNextInvoiceNo(): int
    {
        $today = Carbon::now()->format("Y-m-d");

        $record = Repair::selectRaw("Repairs.*")
            ->whereRaw('InvoiceDate LIKE "%' . $today . '%"')
            ->get();

        return $record->count() + 1;
    }

    /**
     * For new invoice number we have a different format. REPAIR-YYYYMMDDXXXX where XXXX represents a four digit invoice number with leading zeros.
     * We need to special mysql string to get that in search
     *
     * @return string
     */
    private function getInvoiceSearchString(): string
    {
        return 'IF(InvoiceNo REGEXP "^-?[0-9]+$", CONCAT("REPAIR-", DATE_FORMAT(InvoiceDate, "%Y%m%d"), LPAD(InvoiceNo, 4, "0")), "")';
    }
}
