<?php

namespace App\Http\Controllers\DBObjects;

use App\Models\HandsetColors;

class HandsetColorsController extends ObjectTypeNameController
{
    protected function getModel()
    {
        return new HandsetColors();
    }

    protected function getRecordName()
    {
        return "Color";
    }

    protected function getColumnIdInReferenceTables()
    {
        return "ColorId";
    }
}
