<?php

namespace App\Http\Controllers\DBObjects;

use App\Models\HandsetManufacturers;

class HandsetManufacturersController extends ObjectTypeNameController
{
    protected function getModel()
    {
        return new HandsetManufacturers;
    }

    protected function getRecordName()
    {
        return 'Manufacturer';
    }

    protected function getColumnIdInReferenceTables()
    {
        return 'MakeId';
    }
}
