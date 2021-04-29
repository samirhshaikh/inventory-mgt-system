<?php

namespace App\Http\Controllers\Datatables;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class BaseDatatableController extends BaseController
{
    protected function prepareSearch($model, $columns_to_search, $search_text = '', $search_join = 'OR')
    {
        $search_words = explode(' ', $search_text);

        if (!is_array($columns_to_search)) {
            $columns_to_search = [$columns_to_search];
        }

        $conditions = [];
        foreach ($columns_to_search as $column) {
            foreach ($search_words as $search_word) {
                $conditions[] = DB::raw($column) . ' LIKE "%' . $search_word . '%"';
            }
        }

        $model = $search_join === 'OR'
            ? $model->orWhereRaw('(' . join(' OR ', $conditions) . ')')
            : $model->whereRaw('(' . join(' OR ', $conditions) . ')');

        return $model;
    }

    protected function prepareSearchOnRelation(&$query, $columns_to_search, $search_text = '', $where_type = 'AND')
    {
        $search_words = explode(' ', $search_text);

        if (!is_array($columns_to_search)) {
            $columns_to_search = [$columns_to_search];
        }

        $conditions = [];
        foreach ($columns_to_search as $column) {
            foreach ($search_words as $search_word) {
                $conditions[] = DB::raw($column) . ' LIKE "%' . $search_word . '%"';
            }
        }

        if ($where_type === 'OR') {
            $query->orWhereRaw('(' . join(' OR ', $conditions) . ')');
        } else {
            $query->whereRaw('(' . join(' OR ', $conditions) . ')');
        }
    }

    protected function prepareAdvancedSearchQuery($model, $columns_to_search, $search_text = '', $comparision_type = 'anywhere')
    {
        $search_words = explode(' ', $search_text);

        if (!is_array($columns_to_search)) {
            $columns_to_search = [$columns_to_search];
        }

        $conditions = [];
        foreach ($columns_to_search as $column) {
            //If you want search to look for even one word then uncomment the following.
//            foreach ($search_words as $search_word) {
//                $conditions[] = DB::raw($column) . ' LIKE "%' . $search_word . '%"';
//            }
            $conditions[] = $comparision_type === 'anywhere'
                ? DB::raw($column) . ' LIKE "%' . $search_text . '%"'
                : DB::raw($column) . ' = "' . $search_text . '"';
        }
        $model = $model->whereRaw('(' . join(' OR ', $conditions) . ')');

        return $model;
    }

    protected function prepareRecordsOutput($table, $records, $page_no, $search_text = '', $get_all_records = 0)
    {
        $page_size = session('app_settings.framework.page_size', 10);

        $all_records = $records->get();

        $return = [];
        $return['total_rows'] = count($all_records);

        //Check whether user is searching for record on page other than 1
        if ($page_no > 1 && $search_text != '') {
            $start = (($page_no - 1) * $page_size) + 1;

            if ($start >= $return['total_rows']) {
                $page_no = 1;
            }
        }

        if ($get_all_records == 0) {
            $records = $records->skip(($page_no - 1) * $page_size);

            $records = $records->limit($page_size);
        }

        $records = $records->get()
            ->map
            ->transform();

        $return['rows'] = $records
            ->transform(function ($row, $key) use ($table) {
                return $table->rowTransformer($row, $key);
            });

        $return['page_no'] = $page_no;

        return $return;
    }

    protected function searchDataPresent($search_data = [])
    {
        foreach ($search_data as $column => $search_text) {
            if ($search_text != '' || !is_null($search_text)) {
                return true;
            }
        }

        return false;
    }

    protected function getSql($model)
    {
        $replace = function ($sql, $bindings) {
            $needle = '?';
            foreach ($bindings as $replace) {
                $pos = strpos($sql, $needle);
                if ($pos !== false) {
                    if (gettype($replace) === "string") {
                        $replace = ' "' . addslashes($replace) . '" ';
                    }
                    $sql = substr_replace($sql, $replace, $pos, strlen($needle));
                }
            }
            return $sql;
        };
        $sql = $replace($model->toSql(), $model->getBindings());

        return $sql;
    }
}
