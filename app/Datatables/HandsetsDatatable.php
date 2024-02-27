<?php

namespace App\Datatables;

class HandsetsDatatable extends BaseDatatable
{
    public $columns = [
        [
            "name" => "Name",
            "key" => "Name",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 1,
        ],
        [
            "name" => "Make",
            "key" => "manufacturer",
            "enabled" => true,
            "th" => "w-32 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 2,
        ],
        [
            "name" => "Model",
            "key" => "model",
            "enabled" => true,
            "th" => "w-32 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 3,
        ],
        [
            "name" => "Color",
            "key" => "color",
            "enabled" => true,
            "th" => "w-32 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 4,
        ],
        [
            "name" => "Active",
            "key" => "IsActive",
            "enabled" => true,
            "th" => "w-24 text-left sticky",
            "td" => "text-left",
            "type" => "Toggle",
            "route" => "handsets.change-active-status",
            "sorting" => false,
            "searching" => false,
            "order" => 5,
        ],
        [
            "name" => "Date",
            "key" => "UpdatedDate",
            "enabled" => true,
            "th" => "w-56 text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 6,
        ],
        [
            "name" => "Actions",
            "key" => "route",
            "enabled" => true,
            "th" => "w-44 sticky",
            "type" => "HandsetsActions",
            "sorting" => false,
            "searching" => false,
            "order" => 7,
        ],
    ];

    public function options()
    {
        $this->options = [
            "id" => "handsets",
            "url" => route("handsets.data"),
            "pagination" => false,
            "enable_search" => true,
            "primary_key" => "id",
            "record_name" => "Handset",
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
