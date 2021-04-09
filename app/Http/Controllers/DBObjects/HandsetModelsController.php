<?php

namespace App\Http\Controllers\DBObjects;

use App\Models\HandsetModels;

class HandsetModelsController extends ObjectType1 {
    protected function getModel() {
        return new HandsetModels;
    }

    protected function getRecordName() {
        return 'Model';
    }

    protected function getColumnIdInReferenceTables() {
        return 'ModelId';
    }
}
