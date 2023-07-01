<?php

namespace App\Http\Controllers\Datatables;

use App\Http\Controllers\BaseController;

class BaseDatatableController extends BaseController
{
    /**
     * @param $table
     * @param $records
     * @param int $total_rows
     * @param int $page_no
     * @param mixed $search_text
     * @param int $get_all_records
     * @return array
     */
    protected function prepareRecordsOutput(
        $table,
        $records,
        int $total_rows = 0,
        int $page_no,
        $search_text = "",
        int $get_all_records = 0
    ): array {
        $page_size = session("app_settings.framework.page_size", 10);

        $return = [];
        $return["total_rows"] = $total_rows;

        //Check whether user is searching for record on page other than 1
        if ($page_no > 1 && $search_text != "") {
            $start = ($page_no - 1) * $page_size + 1;

            if ($start >= $return["total_rows"]) {
                $page_no = 1;
            }
        }

        //Some request needs all the data
        if (empty($get_all_records)) {
            $records = $records->skip(($page_no - 1) * $page_size);

            $records = $records->limit($page_size);
        }

        $records = $records->get()->map->transform();

        $return["rows"] = $records->transform(function ($row, $key) use (
            $table
        ) {
            return $table->rowTransformer($row, $key);
        });

        $return["page_no"] = (int) $page_no;

        return $return;
    }

    protected function getSql($model)
    {
        $replace = function ($sql, $bindings) {
            $needle = "?";
            foreach ($bindings as $replace) {
                $pos = strpos($sql, $needle);
                if ($pos !== false) {
                    if (gettype($replace) === "string") {
                        $replace = ' "' . addslashes($replace) . '" ';
                    }
                    $sql = substr_replace(
                        $sql,
                        $replace,
                        $pos,
                        strlen($needle)
                    );
                }
            }
            return $sql;
        };
        $sql = $replace($model->toSql(), $model->getBindings());

        return $sql;
    }
}
