<?php

namespace App\Http\Controllers\DBObjects;

use App\Models\HandsetModels;

class HandsetModelsController extends ObjectTypeNameController
{
    protected function getModel()
    {
        return new HandsetModels();
    }

    protected function getRecordName()
    {
        return "Model";
    }

    protected function getColumnIdInReferenceTables()
    {
        return "ModelId";
    }
}
