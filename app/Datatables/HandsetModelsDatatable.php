<?php

namespace App\Datatables;

class HandsetModelsDatatable extends ObjectType1Datatable
{
    protected function getRouteId()
    {
        return 'handset-models';
    }

    protected function getId()
    {
        return 'handset_models';
    }

    protected function getRecordName()
    {
        return 'Model';
    }

    protected function getIcon()
    {
        return 'mobile-alt';
    }
}
