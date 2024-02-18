<?php

namespace App\Datatables;

class PhoneStockDatatable extends BaseDatatable
{
    public $columns = [
        [
            "name" => "Invoice",
            "key" => "InvoiceNo",
            "enabled" => true,
            "th" => "w-32 text-left sticky",
            "td" => "text-left",
            "type" => "InvoiceDetails",
            "sorting" => true,
            "searching" => true,
            "order" => 1,
        ],
        [
            "name" => "Type",
            "key" => "StockType",
            "enabled" => true,
            "th" => "w-1/12 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 2,
        ],
        [
            "name" => "IMEI",
            "key" => "IMEI",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 3,
        ],
        [
            "name" => "Supplier",
            "key" => "supplier",
            "enabled" => true,
            "th" => "w-1/12 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 4,
        ],
        [
            "name" => "Phone",
            "key" => "manufacturer",
            "enabled" => true,
            "th" => "w-32 text-left sticky",
            "td" => "text-left",
            "type" => "Phone",
            "sorting" => true,
            "searching" => true,
            "order" => 5,
        ],
        [
            "name" => "Size",
            "key" => "Size",
            "enabled" => true,
            "th" => "w-16 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 5,
        ],
        [
            "name" => "Cost",
            "key" => "Cost",
            "enabled" => true,
            "th" => "w-16 text-left sticky",
            "td" => "text-left",
            "type" => "Float",
            "sorting" => true,
            "searching" => true,
            "order" => 6,
        ],
        [
            "name" => "Date",
            "key" => "UpdatedDate",
            "enabled" => true,
            "th" => "w-32 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 7,
        ],
        [
            "name" => "Actions",
            "key" => "route",
            "enabled" => true,
            "th" => "w-1/6 sticky",
            "type" => "PhoneStockActions",
            "sorting" => false,
            "searching" => false,
            "order" => 8,
        ],
    ];

    public function options()
    {
        $this->options = [
            "id" => "phonestock",
            "url" => route("phonestock.data"),
            "pagination" => false,
            "enable_search" => true,
            "primary_key" => "Id",
            "record_name" => "Phone",
            "sorting" => [
                "enabled" => true,
                "default" => "UpdatedDate",
                "direction" => "desc",
            ],
        ];

        return $this->options;
    }

    public function rowTransformer(array $row, string $rowKey)
    {
        $row = parent::rowTransformer($row, $rowKey);

        return $row;
    }
}
