<?php

namespace App\Http\Controllers\Datatables;

use App\Datatables\UsersDatatable;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UsersController extends BaseDatatableController
{
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        $table = new UsersDatatable();

        $order_by = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.users.column', Arr::get($table->options(), 'sorting.default'))
            : $request->get('order_by');
        $order_direction = $request->get('order_by', '') == ''
            ? session('app_settings.datatable.sorting.users.direction', Arr::get($table->options(), 'sorting.direction'))
            : 'asc';

        $user_service = new UserService();

        list('total_records' => $total_records, 'records' => $records) = $user_service->getAll(
            $order_by,
            $order_direction,
            $request->get('search_text', '') ?? ''
        );

        return $this->prepareRecordsOutput(
            $table,
            $records,
            $total_records,
            (int)$request->get('page_no', 1),
            $request->get('search_text', ''),
            (int)$request->get('get_all_records', 0)
        );
    }
}
