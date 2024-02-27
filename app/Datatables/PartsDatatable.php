<?php

namespace App\Datatables;

class PartsDatatable extends ObjectType1Datatable
{
    protected function getRouteId()
    {
        return "parts";
    }

    protected function getId()
    {
        return "parts";
    }

    protected function getRecordName()
    {
        return "Part";
    }

    protected function getIcon()
    {
        return "palette";
    }
}
