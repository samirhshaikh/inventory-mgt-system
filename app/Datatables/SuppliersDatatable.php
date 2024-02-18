<?php

namespace App\Datatables;

class SuppliersDatatable extends BaseDatatable
{
    public $columns = [
        [
            "name" => "Name",
            "key" => "SupplierName",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 1,
        ],
        [
            "name" => "Contact No",
            "key" => "ContactNo1",
            "enabled" => true,
            "th" => "w-1/6 text-left sticky",
            "td" => "text-left",
            "type" => "Contact",
            "sorting" => true,
            "searching" => true,
            "order" => 2,
        ],
        [
            "name" => "Balance",
            "key" => "CurrentBalance",
            "enabled" => true,
            "th" => "w-1/6 text-left sticky",
            "td" => "text-left",
            "type" => "Float",
            "sorting" => true,
            "searching" => true,
            "order" => 3,
        ],
        [
            "name" => "Active",
            "key" => "IsActive",
            "enabled" => true,
            "th" => "w-1/12 text-left sticky",
            "td" => "text-left",
            "type" => "Toggle",
            "route" => "suppliers.change-active-status",
            "sorting" => false,
            "searching" => false,
            "order" => 4,
        ],
        [
            "name" => "Date",
            "key" => "UpdatedDate",
            "enabled" => true,
            "th" => "w-1/6 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 5,
        ],
        [
            "name" => "Actions",
            "key" => "route",
            "enabled" => true,
            "th" => "w-1/6 sticky",
            "type" => "SuppliersActions",
            "sorting" => false,
            "searching" => false,
            "order" => 6,
        ],
    ];

    public function options()
    {
        $this->options = [
            "id" => "suppliers",
            "url" => route("suppliers.data"),
            "pagination" => false,
            "enable_search" => true,
            "primary_key" => "Id",
            "record_name" => "Supplier",
            "sorting" => [
                "enabled" => true,
                "default" => "UpdatedDate",
                "direction" => "desc",
            ],
            "cache_data" => true,
        ];

        return $this->options;
    }

    public function rowTransformer(array $row, string $rowKey)
    {
        $row = parent::rowTransformer($row, $rowKey);

        return $row;
    }
}
