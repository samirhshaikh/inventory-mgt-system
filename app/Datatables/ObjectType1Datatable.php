<?php

namespace App\Datatables;

abstract class ObjectType1Datatable extends BaseDatatable
{
    abstract protected function getRouteId();

    abstract protected function getId();

    abstract protected function getRecordName();

    abstract protected function getIcon();

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
            "name" => "Active",
            "key" => "IsActive",
            "enabled" => true,
            "th" => "w-1/12 text-left sticky",
            "td" => "text-left",
            "type" => "Toggle",
            "route" => "%s.change-active-status",
            "sorting" => false,
            "searching" => false,
            "order" => 2,
        ],
        [
            "name" => "Date",
            "key" => "UpdatedDate",
            "enabled" => true,
            "th" => "text-left sticky",
            "td" => "text-left",
            "sorting" => true,
            "searching" => true,
            "order" => 3,
        ],
        [
            "name" => "Actions",
            "key" => "route",
            "enabled" => true,
            "th" => "text-left sticky",
            "type" => "ObjectTypeNameActions",
            "sorting" => false,
            "searching" => false,
            "order" => 4,
        ],
    ];

    public function columns()
    {
        for ($i = 0; $i < count($this->columns); $i++) {
            if (array_key_exists("route", $this->columns[$i])) {
                $this->columns[$i]["route"] = sprintf(
                    $this->columns[$i]["route"],
                    $this->getRouteId()
                );
            }
        }

        return $this->columns;
    }

    public function options()
    {
        $this->options = [
            "id" => $this->getId(),
            "url" => route("datatable." . $this->getRouteId() . ".data"),
            "pagination" => false,
            "enable_search" => true,
            "primary_key" => "Id",
            "record_name" => $this->getRecordName(),
            "routes" => [
                "delete" => route($this->getRouteId() . ".delete"),
                "get-single" => route($this->getRouteId() . ".get-single"),
                "save" => route($this->getRouteId() . ".save"),
                "check-duplicate-name" => route(
                    $this->getRouteId() . ".check-duplicate-name"
                ),
            ],
            "icon" => $this->getIcon(),
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
