<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\HandsetsDatatable;
use App\Http\Controllers\Datatables\BaseDatatableController;
use App\Models\Handsets;
use Illuminate\Http\Request;

class HandsetsController extends BaseDatatableController
{
    public function getData(Request $request)
    {
        $table = new HandsetsDatatable();

        $order_by = session('app_settings.datatable.sorting.handsets.column', array_get($table->options(), 'sorting.default'));
        $order_direction = strtoupper(session('app_settings.datatable.sorting.handsets.direction', array_get($table->options(), 'sorting.direction')));

        $handsets = Handsets::selectRaw('HandsetMaster.*, ManufactureMaster.Name as manufacturer, ColorMaster.Name as color, modelmaster.Name as model')
            ->join('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
            ->join('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
            ->join('modelmaster', 'modelmaster.Id', '=', 'ModelId')
            ->orderBy($order_by, $order_direction)
            ->get()
        ;

        $handsets = $handsets->map->transform();

        $return = [];
        $return['rows'] = $handsets
            ->transform(function ($row, $key) use ($table) {
                return $table->rowTransformer($row, $key);
            });

        return $return;
    }
}
