<?php

namespace App\Datatables;

class HandsetColorsDatatable extends ObjectType1Datatable {
    protected function getRouteId() {
        return 'handset-colors';
    }

    protected function getId() {
        return 'handset_colors';
    }

    protected function getRecordName() {
        return 'Color';
    }

    protected function getIcon() {
        return 'palette';
    }
}