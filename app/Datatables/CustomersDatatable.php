<?php

namespace App\Datatables;

class CustomersDatatable extends BaseDatatable
{
    public $columns = [
        [
            "name" => "Name",
            "key" => "CustomerName",
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
            "key" => "Balance",
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
            "route" => "customers.change-active-status",
            "sorting" => false,
            "searching" => false,
            "order" => 3,
        ],
        [
            "name" => "Date",
            "key" => "UpdatedDate",
            "enabled" => true,
            "th" => "w-1/6 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 4,
        ],
        [
            "name" => "Actions",
            "key" => "route",
            "enabled" => true,
            "th" => "w-1/6 sticky",
            "type" => "CustomersActions",
            "sorting" => false,
            "searching" => false,
            "order" => 5,
        ],
    ];

    public function options()
    {
        $this->options = [
            "id" => "customers",
            "url" => route("customers.data"),
            "pagination" => false,
            "enable_search" => true,
            "primary_key" => "id",
            "record_name" => "Customer",
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
