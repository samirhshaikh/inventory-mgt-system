<?php

namespace App\Http\Controllers\Datatables;

use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BaseDatatableController extends BaseController
{
    /**
     * @param mixed $model
     * @param mixed $columns_to_search
     * @param string $search_text
     * @param string $search_join
     * @return Builder
     */
    protected function prepareSearch($model, $columns_to_search, string $search_text = '', string $search_join = 'OR'): Builder
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

    /**
     * @param mixed $query
     * @param mixed $columns_to_search
     * @param string $search_text
     * @param string $where_type
     * @return Builder
     */
    protected function prepareSearchOnRelation(&$query, $columns_to_search, string $search_text = '', string $where_type = 'AND'): Builder
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

    /**
     * @param mixed $model
     * @param $columns_to_search
     * @param string $search_text
     * @param string $comparison_type
     * @return Builder
     */
    protected function prepareAdvancedSearchQuery($model, $columns_to_search, string $search_text = '', string $comparison_type = 'anywhere'): Builder
    {
        if (!is_array($columns_to_search)) {
            $columns_to_search = [$columns_to_search];
        }

        $conditions = [];
        foreach ($columns_to_search as $column) {
            //If you want search to look for even one word then uncomment the following.
//            foreach (explode(' ', $search_text) as $search_word) {
//                $conditions[] = DB::raw($column) . ' LIKE "%' . $search_word . '%"';
//            }
            $conditions[] = $comparison_type === 'anywhere'
                ? DB::raw($column) . ' LIKE "%' . $search_text . '%"'
                : DB::raw($column) . ' = "' . $search_text . '"';
        }
        $model = $model->whereRaw('(' . join(' OR ', $conditions) . ')');

        return $model;
    }

    /**
     * @param $table
     * @param $records
     * @param int $total_rows
     * @param int $page_no
     * @param mixed $search_text
     * @param int $get_all_records
     * @return array
     */
    protected function prepareRecordsOutput($table, $records, int $total_rows = 0, int $page_no, $search_text = '', int $get_all_records = 0): array
    {
        $page_size = session('app_settings.framework.page_size', 10);

        $return = [];
        $return['total_rows'] = $total_rows;

        //Check whether user is searching for record on page other than 1
        if ($page_no > 1 && $search_text != '') {
            $start = (($page_no - 1) * $page_size) + 1;

            if ($start >= $return['total_rows']) {
                $page_no = 1;
            }
        }

        //Some request needs all the data
        if (empty($get_all_records)) {
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

        $return['page_no'] = (int)$page_no;

        return $return;
    }

    /**
     * @param string $search_data
     * @return bool
     */
    protected function searchDataPresent(string $search_data = '{}'): bool
    {
        foreach (json_decode($search_data) as $column => $search_text) {
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
