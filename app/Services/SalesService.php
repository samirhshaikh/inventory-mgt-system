<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\ReturnItemRequest;
use App\Http\Requests\SaveSaleRequest;
use App\Models\PhoneStock;
use App\Models\Sales;
use App\Models\SalesStock;
use App\Models\StockLog;
use App\Models\TradeIn;
use App\Traits\SearchTrait;
use App\Traits\TableActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SalesService
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

        $records = new Sales();

        $records = $records->leftJoin(
            "Customer_Sales",
            "Customer_Sales.Id",
            "=",
            "CustomerId"
        );

        if (!is_null($invoice_ids)) {
            $records = $records->whereIn("Sales.Id", $invoice_ids);
        }

        //Get total records
        $total_records = $this->getTotalRecords(clone $records);

        $records = $records->with([
            "sales" => function ($query) {
                $query->orderBy("IMEI", "ASC");
            },
        ]);

        $records = $records->with("tradein");

        switch ($order_by) {
            case "IMEI":
                break;
            case "children":
                $records = $records
                    ->addSelect(
                        DB::raw(
                            "SUM(CASE WHEN Return = 0 THEN 0 ELSE SalesStock.Cost) as Total_Cost"
                        )
                    )
                    ->leftJoin(
                        "SalesStock",
                        "SalesStock.InvoiceId",
                        "=",
                        "Sales.Id"
                    )
                    ->groupBy("Sales.Id")
                    ->orderBy("Total_Cost", $order_direction);
                break;
            case "customer.CustomerName":
                $records = $records->leftJoin(
                    "Customer_Sales",
                    "Customer_Sales.Id",
                    "=",
                    "CustomerId"
                );
                break;
            case "UpdatedDate":
                $records = $records->orderBy(
                    "Sales.UpdatedDate",
                    $order_direction
                );
                break;
            default:
                $records = $records->orderBy($order_by, $order_direction);
        }

        $records = $records->addSelect("Sales.*");

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
            $records = Sales::selectRaw("Sales.Id");

            if (
                ($search_type === "simple" && $search_text != "") ||
                ($search_type === "advanced" &&
                    $this->searchDataPresent($search_data))
            ) {
                $records = $records
                    ->leftJoin(
                        "Customer_Sales",
                        "Customer_Sales.Id",
                        "=",
                        "CustomerId"
                    )
                    ->join(
                        "SalesStock",
                        "SalesStock.InvoiceId",
                        "=",
                        "Sales.Id"
                    )
                    ->join(
                        "PhoneStock",
                        "PhoneStock.IMEI",
                        "=",
                        "SalesStock.IMEI"
                    )
                    ->join(
                        "ManufactureMaster",
                        "ManufactureMaster.Id",
                        "=",
                        "MakeId"
                    )
                    ->join("ColorMaster", "ColorMaster.Id", "=", "ColorId")
                    ->join("ModelMaster", "ModelMaster.Id", "=", "ModelId");
            }

            if ($search_type === "simple" && $search_text != "") {
                $fields_to_search = [
                    $this->getInvoiceSearchString(),
                    "InvoiceNo",
                    'DATE_FORMAT(InvoiceDate, "%d-%b-%Y")',
                    "Customer_Sales.CustomerName",
                    "Customer_Sales.ContactNo1",
                    "Customer_Sales.ContactNo2",
                    "SalesStock.IMEI",
                    "ManufactureMaster.Name",
                    "ColorMaster.Name",
                    "ModelMaster.Name",
                    "Size",
                    "SalesStock.Cost",
                    "ModelNo",
                    "Network",
                    "Sales.Comments",
                    'DATE_FORMAT(Sales.CreatedDate, "%d-%b-%Y")',
                    'DATE_FORMAT(Sales.UpdatedDate, "%d-%b-%Y")',
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

            $records = $records->orderBy("Sales.Id", "ASC")->get();

            return $records->pluck("Id")->all();
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
                            "Customer_Sales.CustomerName",
                            "Customer_Sales.ContactNo1",
                            "Customer_Sales.ContactNo2",
                        ],
                        $search_text
                    );
                    break;
                case "contact":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        [
                            "Customer_Sales.ContactNo1",
                            "Customer_Sales.ContactNo2",
                        ],
                        $search_text
                    );
                    break;
                case "InvoiceDate":
                    list($start_date, $end_date) = explode(",", $search_text);
                    if ($end_date) {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            "Sales.InvoiceDate",
                            [
                                DateService::convertToMySQL($start_date),
                                DateService::convertToMySQL($end_date),
                            ],
                            "date_range"
                        );
                    } else {
                        $model = $this->prepareAdvancedSearchQuery(
                            $model,
                            'DATE_FORMAT(Sales.InvoiceDate, "%d-%b-%Y")',
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
                        "SalesStock.IMEI",
                        $search_text
                    );
                    break;
                case "Cost":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "Sales." . $column,
                        $search_text
                    );
                    break;
                case "PaymentMethod":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "Sales." . $column,
                        $search_text,
                        "exact_match"
                    );
                    break;
                case "Size":
                case "Network":
                case "ModelNo":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        "SalesStock." . $column,
                        $search_text
                    );
                    break;
                case "UpdatedDate":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        [
                            'DATE_FORMAT(Sales.CreatedDate, "%d-%b-%Y")',
                            'DATE_FORMAT(Sales.UpdatedDate, "%d-%b-%Y")',
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
     * For new invoice number we have a different format. INV-YYYYMMDDXXXX where XXXX represents a four digit invoice number with leading zeros.
     * We need to special mysql string to get that in search
     *
     * @return string
     */
    private function getInvoiceSearchString(): string
    {
        return 'IF(InvoiceNo REGEXP "^-?[0-9]+$", CONCAT("INV-", DATE_FORMAT(InvoiceDate, "%Y%m%d"), LPAD(InvoiceNo, 4, "0")), "")';
    }

    /**
     * @param IdRequest $request
     * @return mixed
     * @throws RecordNotFoundException
     */
    public function getSingleSales(IdRequest $request): mixed
    {
        $record = Sales::where("Sales.Id", $request->get("Id"))
            ->with("customer")
            ->where("Id", $request->get("Id"));

        $record = $record->with([
            "sales" => function ($query) {
                $query->orderBy("IMEI", "ASC");
            },
        ]);

        $record = $record->with("tradein")->get();

        if ($record->count()) {
            return $record->map->transform()->first();
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param SaveSaleRequest $request
     * @return int
     * @throws RecordNotFoundException
     */
    public function save(SaveSaleRequest $request): int
    {
        /*
         * First we will check for any errors. If yes then return the error code.
         * If no error is found then perform the operation.
         */

        //Check whether the sale being edited exist or not.
        if ($request->get("operation", "add") == "edit") {
            $record = Sales::where("Id", $request->get("Id"))->get();
            if (!$record->count()) {
                throw new RecordNotFoundException();
            }
        }

        //Now delete the child phones
        if (
            $request->get("operation", "add") == "edit" &&
            count($request->get("children_to_delete", []))
        ) {
            $phonestock_service = new PhoneStockService();

            foreach ($request->get("children_to_delete", []) as $row) {
                $phone = SalesStock::where("Id", $row["Id"])->get();
                if ($phone->count()) {
                    $phone = $phone->first();

                    //Add an entry to stock_log
                    $stocklog_service = new StockLogService();
                    $stocklog_service->add(
                        $phone["IMEI"],
                        StockLog::ACTIVITY_DELETED
                    );

                    //Change the status in phonestock
                    $phonestock_service->changePhoneAvailabilityStatus(
                        $row["IMEI"]
                    );

                    SalesStock::where("Id", $row["Id"])->delete();
                }
            }
        }

        //Create new record in sales table
        if ($request->get("operation", "add") == "edit") {
            $record = $record->first();
        } else {
            $record = new Sales();

            $record->InvoiceNo = $this->getNextInvoiceNo();
            $record->CreatedBy = session("user_details.UserName");
            $record->IsActive = 1;
        }

        $record->InvoiceDate = Carbon::createFromFormat(
            "d-M-Y",
            $request->get("InvoiceDate")
        )->toDateTimeString();
        $record->CustomerId = $request->get("CustomerId");
        $record->PaymentMethod = $request->get("PaymentMethod");
        $record->VAT = number_format($request->get("VAT", 0), 2);
        $record->Comments = $request->get("Comments");
        $record->UpdatedBy = session("user_details.UserName");
        $record->save();

        if ($request->get("operation", "add") == "add") {
            $record->Id = Sales::lastInsertId();
        }

        $salesstock_service = new SalesStockService();

        //Create/Update records in salesstock table
        $salesstock_service->save($record->Id, $request->get("children", []));

        //Add the record in tradein
        if ($request->get("tradein", [])) {
            $tradein_data = $request->get("tradein");
            if ($tradein_data["PurchaseInvoiceId"]) {
                $tradein_service = new TradeInService();
                $tradein_service->save(
                    $record->Id,
                    $tradein_data["PurchaseInvoiceId"]
                );
            }
        }

        return $record->Id;
    }

    /**
     * @param IdRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    public function delete(IdRequest $request): bool
    {
        //Check whether the record exist or not
        $invoice = Sales::where("Id", $request->get("Id"))->get();

        if ($invoice->count()) {
            $phonestock_service = new PhoneStockService();

            $invoice = $invoice->first();

            //Get all the phones in this invoice
            $phones = SalesStock::where("InvoiceId", $invoice->Id)->get();
            if ($phones->count()) {
                foreach ($phones as $phone) {
                    //Add an entry to stock_log
                    $stocklog_service = new StockLogService();
                    $stocklog_service->add(
                        $phone["IMEI"],
                        StockLog::ACTIVITY_DELETED
                    );

                    //Change the status in phonestock
                    $phonestock_service->changePhoneAvailabilityStatus(
                        $phone["IMEI"]
                    );

                    SalesStock::where("Id", $phone->Id)->delete();
                }
            }

            Sales::where("Id", $request->get("Id"))->delete();

            TradeIn::where("SalesInvoiceId", $request->get("Id"))->delete();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param ReturnItemRequest $request
     * @return bool
     * @throws RecordNotFoundException
     */
    public function returnItem(ReturnItemRequest $request): bool
    {
        $phonestock_service = new PhoneStockService();

        try {
            //Change the status in phonestock
            $phonestock_service->changePhoneAvailabilityStatus(
                $request->get("IMEI")
            );

            //Mark the entry in salesstock as returned.
            $sale_stock = SalesStock::where(
                "InvoiceId",
                $request->get("InvoiceId")
            )
                ->where("IMEI", $request->get("IMEI"))
                ->get();
            if ($sale_stock->count()) {
                $sale_stock = $sale_stock->first();

                $sale_stock->Returned = true;
                $sale_stock->ReturnedDate = Carbon::createFromFormat(
                    "d-M-Y",
                    $request->get("ReturnedDate")
                )->toDateTimeString();
                $sale_stock->save();
            }

            //Add an entry to stock_log
            $stocklog_service = new StockLogService();
            $stocklog_service->add(
                $request->get("IMEI"),
                StockLog::ACTIVITY_RETURNED,
                $request->get("Comments") ?? ""
            );

            return true;
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @return int
     */
    public function getNextInvoiceNo(): int
    {
        $today = Carbon::now()->format("Y-m-d");

        $record = Sales::selectRaw("Sales.*")
            ->whereRaw('InvoiceDate LIKE "%' . $today . '%"')
            ->get();

        return $record->count() + 1;
    }

    /**
     * @param string $start
     * @param string $end
     * @return int
     */
    public function getSalesForPeriod($start = "", $end = ""): int
    {
        $record = Sales::selectRaw("SUM(Cost) as total")->join(
            "SalesStock",
            "SalesStock.InvoiceId",
            "=",
            "Sales.Id"
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
