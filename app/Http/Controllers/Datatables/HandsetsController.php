<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\HandsetsDatatable;
use App\Models\Handsets;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class HandsetsController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new HandsetsDatatable();

        $order_by = session('app_settings.datatable.sorting.handsets.column', Arr::get($table->options(), 'sorting.default'));
        $order_direction = strtoupper(session('app_settings.datatable.sorting.handsets.direction', Arr::get($table->options(), 'sorting.direction')));

        $handsets = Handsets::selectRaw('HandsetMaster.*, ManufactureMaster.Name as manufacturer, ColorMaster.Name as color, modelmaster.Name as model')
            ->join('ManufactureMaster', 'ManufactureMaster.Id', '=', 'MakeId')
            ->join('ColorMaster', 'ColorMaster.Id', '=', 'ColorId')
            ->join('modelmaster', 'modelmaster.Id', '=', 'ModelId')
            ->orderBy($order_by, $order_direction)
            ->get();

        $handsets = $handsets->map->transform();

        $return = [];
        $return['rows'] = $handsets
            ->transform(function ($row, $key) use ($table) {
                return $table->rowTransformer($row, $key);
            });

        return $return;
    }
}
