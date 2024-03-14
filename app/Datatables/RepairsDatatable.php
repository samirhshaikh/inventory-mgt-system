<?php

namespace App\Datatables;

class RepairsDatatable extends BaseDatatable
{
    public $columns = [
        [
            "name" => "",
            "key" => "",
            "enabled" => true,
            "th" => "w-2 text-left sticky",
            "td" => "text-left",
            "type" => "ExpandCollapse",
            "sorting" => false,
            "searching" => false,
            "order" => 1,
        ],
        [
            "name" => "Invoice No",
            "key" => "InvoiceNo",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 2,
        ],
        [
            "name" => "Invoice Date",
            "key" => "InvoiceDate",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 3,
        ],
        [
            "name" => "Received Date",
            "key" => "ReceivedDate",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 4,
        ],
        [
            "name" => "Customer",
            "key" => "customer.CustomerName",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 5,
        ],
        [
            "name" => "Phone",
            "key" => "phone_details",
            "enabled" => true,
            "th" => "w-32 text-left sticky",
            "td" => "text-left",
            "type" => "Phone",
            "sorting" => true,
            "searching" => true,
            "order" => 6,
        ],
        [
            "name" => "Cost",
            "key" => "Amount",
            "enabled" => true,
            "th" => "text-right sticky",
            "td" => "text-right",
            "type" => "Float",
            "sorting" => true,
            "searching" => true,
            "order" => 7,
        ],
        [
            "name" => "Actions",
            "key" => "route",
            "enabled" => true,
            "th" => "w-20 sticky",
            "type" => "RepairsActions",
            "sorting" => false,
            "searching" => false,
            "order" => 8,
        ],
    ];

    public $child_columns = [
        [
            "name" => "",
            "key" => "",
            "enabled" => true,
            "th" => "text-left",
            "td" => "text-left",
            "sorting" => false,
            "searching" => false,
            "order" => 1,
        ],
        [
            "name" => "Supplier",
            "key" => "supplier.SupplierName",
            "enabled" => true,
            "th" => "text-left",
            "td" => "text-left",
            "column_span" => 3,
            "sorting" => false,
            "searching" => true,
            "order" => 2,
        ],
        [
            "name" => "Part",
            "key" => "part.Name",
            "enabled" => true,
            "th" => "text-left",
            "td" => "text-left",
            "column_span" => 2,
            "sorting" => false,
            "searching" => true,
            "order" => 3,
        ],
        [
            "name" => "Cost",
            "key" => "Cost",
            "enabled" => true,
            "th" => "text-right",
            "td" => "text-right",
            "column_span" => 2,
            "sorting" => false,
            "searching" => true,
            "type" => "Float",
            "order" => 4,
        ],
    ];

    public function options()
    {
        $this->options = [
            "id" => "repairs",
            "url" => route("repairs.data"),
            "pagination" => false,
            "enable_search" => true,
            "primary_key" => "id",
            "record_name" => "Repair",
            "child_record_name" => "Repair",
            "sorting" => [
                "enabled" => true,
                "default" => "UpdatedDate",
                "direction" => "desc",
            ],
        ];

        return $this->options;
    }

    /**
     * @param array $row
     * @param string $rowKey
     * @return array|mixed
     */
    public function rowTransformer(array $row, string $rowKey)
    {
        $row = parent::rowTransformer($row, $rowKey);

        return $row;
    }
}
