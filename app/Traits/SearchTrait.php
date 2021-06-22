<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait SearchTrait
{
    /**
     * @param string $search_data
     * @return bool
     */
    protected function searchDataPresent(string $search_data = '{}'): bool
    {
        foreach (json_decode($search_data) as $column => $search_text) {
            if (!is_null($search_text) && strlen(strval($search_text))) {
                return true;
            }
        }

        return false;
    }

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
}
