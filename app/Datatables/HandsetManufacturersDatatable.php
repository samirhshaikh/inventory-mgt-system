<?php

namespace App\Datatables;

class HandsetManufacturersDatatable extends ObjectType1Datatable {
    protected function getRouteId() {
        return 'handset-manufacturers';
    }

    protected function getId() {
        return 'handset_manufacturers';
    }

    protected function getRecordName() {
        return 'Manufacturer';
    }

    protected function getIcon() {
        return 'industry';
    }
}