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
use App\Traits\SearchTrait;
use App\Traits\TableActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PurchaseService
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

        $records = new Purchase();

        $records = $records->with("supplier");

        $records = $records->with([
            "purchases" => function ($query) {
                $query->orderBy("IMEI", "ASC");
            },
        ]);

        if (!is_null($invoice_ids)) {
            $records = $records->whereIn("Purchase.id", $invoice_ids);
        }

        //Get total records
        $total_records = $this->getTotalRecords(clone $records);

        switch ($order_by) {
            case "IMEI":
                break;
            case "children":
                $records = $records
                    ->addSelect(
                        DB::raw("*, SUM(PhoneStock.Cost) as Total_Cost")
                    )
                    ->leftJoin(
                        "PhoneStock",
                        "PhoneStock.InvoiceId",
                        "=",
                        "Purchase.id"
                    )
                    ->groupBy("Purchase.id")
                    ->orderBy("Total_Cost", $order_direction);
                break;
            case "supplier.SupplierName":
                $records = $records->leftJoin(
                    "Supplier",
                    "Supplier.id",
                    "=",
                    "SupplierId"
                );
                break;
            default:
                $records = $records->orderBy($order_by, $order_direction);
        }

        $records = $records->select("Purchase.*");

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
            $records = Purchase::selectRaw("Purchase.id");

            if (
                ($search_type === "simple" && $search_text != "") ||
                ($search_type === "advanced" &&
                    $this->searchDataPresent($search_data))
            ) {
                $records = $records
                    ->join("Supplier", "Supplier.id", "=", "SupplierId")
                    ->join(
                        "PhoneStock",
                        "PhoneStock.InvoiceId",
                        "=",
                        "Purchase.id"
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
                    "InvoiceNo",
                    'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                    "Supplier.SupplierName",
                    "Supplier.ContactNo1",
                    "Supplier.ContactNo2",
                    "IMEI",
                    "ManufactureMaster.Name",
                    "ColorMaster.Name",
                    "ModelMaster.Name",
                    "Size",
                    "Cost",
                    "StockType",
                    "ModelNo",
                    "Network",
                    "Purchase.Comments",
                    "Status",
                    'DATE_FORMAT(Purchase.CreatedDate, "%d-%b-%Y")',
                    'DATE_FORMAT(Purchase.UpdatedDate, "%d-%b-%Y")',
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
            }

            $records = $records->orderBy("Purchase.id", "ASC")->get();

            return $records->pluck("id")->all();
        } catch (\Exception $e) {
            return [""];
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
                case "supplier":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "Supplier.SupplierName",
                        $search_text
                    );
                    break;
                case "InvoiceDate":
                    list($start_date, $end_date) = explode(",", $search_text);
                    if ($end_date) {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            "InvoiceDate",
                            [
                                DateService::convertToMySQL($start_date),
                                DateService::convertToMySQL($end_date),
                            ],
                            "date_range"
                        );
                    } else {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                            $search_text,
                            "exact_match"
                        );
                    }
                    break;
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
                case "phones":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "PhoneStock.IMEI",
                        $search_text
                    );
                    break;
                case "IMEI":
                case "InvoiceNo":
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
                            'DATE_FORMAT(Purchase.CreatedDate, "%d-%b-%Y")',
                            'DATE_FORMAT(Purchase.UpdatedDate, "%d-%b-%Y")',
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
    public function getSinglePurchase(IdRequest $request): mixed
    {
        $record = new Purchase();

        $record = $record->where("id", $request->get("id"));

        $record = $record->with("supplier");

        $record = $record->with([
            "purchases" => function ($query) {
                $query->orderBy("IMEI", "ASC");
            },
        ]);

        $record = $record->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param SavePurchaseRequest $request
     * @return array
     * @throws DuplicateIMEIException
     * @throws InvalidDataException
     * @throws RecordNotFoundException
     * @throws ReferenceException
     */
    public function save(SavePurchaseRequest $request): array
    {
        /*
         * First we will check for any errors. If yes then return the error code.
         * If no error is found then perform the operation.
         */

        if (count($request->get("children", [])) == 0) {
            throw new InvalidDataException();
        }

        //Check whether the phones marked for deletion are having any reference in other tables or not.
        if (
            $request->get("operation", "add") == "edit" && //Delete is only applicable in existing purchases
            count($request->get("children_to_delete", [])) &&
            $this->phoneFoundInForeignTable($request->get("children_to_delete")) //Checking in reference tables
        ) {
            throw new ReferenceException();
        }

        $phonestock_service = new PhoneStockService();

        //Check for duplicate imei
        foreach ($request->get("children", []) as $row) {
            if (
                $phonestock_service->isDuplicateIMEI(
                    $row["IMEI"],
                    $row["id"] ?? 0
                )
            ) {
                throw new DuplicateIMEIException();
            }
        }

        //Check whether the purchase being edited exist or not.
        if ($request->get("operation", "add") == "edit") {
            $record = Purchase::where("id", $request->get("id"))->get();
            if (!$record->count()) {
                throw new RecordNotFoundException();
            }
        }

        //Now delete the child phones
        if (
            $request->get("operation", "add") == "edit" &&
            count($request->get("children_to_delete", []))
        ) {
            foreach ($request->get("children_to_delete", []) as $row) {
                PhoneStock::where("id", $row["id"])->delete();
            }
        }

        //Create new record in purchase table
        if ($request->get("operation", "add") == "edit") {
            $record = $record->first();
        } else {
            $record = new Purchase();

            $record->CreatedBy = session("user_details.UserName");
            $record->IsActive = 1;
        }

        $record->InvoiceNo = $request->get("InvoiceNo");
        $record->InvoiceDate = Carbon::createFromFormat(
            "d-M-Y",
            $request->get("InvoiceDate")
        )->toDateTimeString();
        $record->SupplierId = $request->get("SupplierId");
        $record->Comments = $request->get("Comments");
        $record->UpdatedBy = session("user_details.UserName");
        $record->save();

        if ($request->get("operation", "add") == "add") {
            $record->id = Purchase::lastInsertId();
        }

        //Create/Update records in phonestock table
        $records_count = $phonestock_service->save(
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
        $invoice = Purchase::where("id", $request->get("id"))->get();

        if ($invoice->count()) {
            $invoice = $invoice->first();

            if (
                $this->foreignReferenceFound(
                    ["TradeIn"],
                    "PurchaseInvoiceId",
                    $request->get("id")
                )
            ) {
                throw new ReferenceException();
            }

            //Get all the phones in this invoice
            $phones = PhoneStock::where("InvoiceId", $invoice->id)->get();
            if ($phones->count()) {
                $tables_to_check = ["SalesStock"];
                foreach ($phones as $phone) {
                    if (
                        $this->foreignReferenceFound(
                            $tables_to_check,
                            "IMEI",
                            $phone->IMEI
                        )
                    ) {
                        throw new ReferenceException();
                    }
                }

                foreach ($phones as $phone) {
                    PhoneStock::where("id", $phone->id)->delete();
                }
            }

            Purchase::where("id", $request->get("id"))->delete();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param array $phone_ids
     * @return bool
     */
    protected function phoneFoundInForeignTable(array $phone_ids): bool
    {
        $tables_to_check = ["SalesStock"];
        foreach ($phone_ids as $phone_id) {
            if (
                $this->foreignReferenceFound(
                    $tables_to_check,
                    "IMEI",
                    $phone_id
                )
            ) {
                return true;
            }
        }

        return false;
    }

    public function getPurchaseForPeriod($start = "", $end = "")
    {
        $record = Purchase::selectRaw("SUM(Cost) as total")->join(
            "PhoneStock",
            "PhoneStock.InvoiceId",
            "=",
            "Purchase.id"
        );

        if ($start) {
            $record = $record->whereRaw(
                sprintf('DATE(InvoiceDate) >= "%s"', $start)
            );
        }

        if ($end) {
            $record = $record->whereRaw(
                sprintf('DATE(InvoiceDate) <= "%s"', $end)
            );
        }

        $record = $record->get()->first();

        return $record->total ?? 0;
    }
}
