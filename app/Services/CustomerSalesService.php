<?php

namespace App\Services;

use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReferenceException;
use App\Http\Requests\IdRequest;
use App\Http\Requests\SaveCustomerSalesRequest;
use App\Models\CustomerSales;
use App\Traits\SearchTrait;
use App\Traits\TableActions;
use Illuminate\Database\Eloquent\Builder;

class CustomerSalesService
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
        $records = new CustomerSales();

        if ($search_type === "simple" && $search_text != "") {
            $columns_to_search = [
                "CustomerName",
                "ContactNo1",
                "ContactNo2",
                "Address",
                "City",
                "Balance",
                "Comments",
                'DATE_FORMAT(CreatedDate, "%d-%b-%Y")',
                'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")',
            ];

            $records = $this->prepareSearch(
                $records,
                $columns_to_search,
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
                case "CustomerName":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        $column,
                        $search_text
                    );
                    break;
                case "ContactNo":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        ["ContactNo1", "ContactNo2"],
                        $search_text
                    );
                    break;
                case "UpdatedDate":
                    $model = $this->prepareAdvancedSearchQuery(
                        $model,
                        [
                            'DATE_FORMAT(CreatedDate, "%d-%b-%Y")',
                            'DATE_FORMAT(UpdatedDate, "%d-%b-%Y")',
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
        $record = CustomerSales::where("Id", $request->get("Id"))->get();

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
            return $this->changeRecordStatus(new CustomerSales(), $request);
        } catch (RecordNotFoundException $e) {
            throw new RecordNotFoundException();
        }
    }

    /**
     * @param SaveCustomerSalesRequest $request
     * @return int
     * @throws RecordNotFoundException
     */
    public function save(SaveCustomerSalesRequest $request): int
    {
        $record = CustomerSales::where("Id", $request->get("Id"))->get();

        if ($request->get("operation", "add") == "edit") {
            if (!$record->count()) {
                throw new RecordNotFoundException();
            }

            $record = $record->first();
        } else {
            $record = new CustomerSales();

            $record->CreatedBy = session("user_details.UserName");
        }

        $record->CustomerName = $request->get("CustomerName");
        $record->ContactNo1 = $request->get("ContactNo1");
        $record->ContactNo2 = $request->get("ContactNo2");
        $record->Comments = $request->get("Comments");
        $record->Balance = (float) $request->get("Balance");
        $record->UpdatedBy = session("user_details.UserName");
        $record->IsActive = $request->get("IsActive");
        $record->save();

        return $request->get("operation", "add") == "edit"
            ? $request->get("Id")
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
        $record = CustomerSales::where("Id", $request->get("Id"));

        if ($record->get()->count()) {
            $tables_to_check = ["Sales"];
            if (
                $this->foreignReferenceFound(
                    $tables_to_check,
                    "CustomerId",
                    $request->get("Id")
                )
            ) {
                throw new ReferenceException();
            }

            $record->delete();

            return true;
        } else {
            throw new RecordNotFoundException();
        }
    }
}
