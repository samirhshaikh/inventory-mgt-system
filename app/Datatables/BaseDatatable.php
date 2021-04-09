<?php

namespace App\Datatables;

use NumberFormatter;

abstract class BaseDatatable {
    public $options = [];
    public $columns = [];

    public function __construct() {
        $this->options();
        $this->columns();
    }

    abstract public function options();

    public function columns() {
        return $this->columns;
    }

    public function rowTransformer(array $row, string $rowKey) {
//        $unformratted_values = [];

        //transform the keys
        foreach ($this->columns as $colKey => $column) {
            //if we don't have a value then just carry on
            if (array_get($row, $column['key'], null) === null) {
                clock()->info([$column['key'], 'skipped with no data', $row]);
                continue;
            }

            $value = array_get($row, $column['key']);

//            array_set($unformratted_values, $column['key'], $value);

            //apply rules if needed
            if (!empty($column['rules'])) {
                array_set($row, $column['key'], $this->processRules($value, $column['rules']));
            }

            //do a check if the type is a actual file
            if (($type = array_get($column, 'type', null)) !== null) {
                if (!file_exists(base_path('resources/js/Datatable/Cells/' . $type . '.vue'))) {
                    clock()->error(`$type does not exist as Datatable cell`);
                    array_set($column, 'type', null);
                }
            }

//            $row['unformatted_values'] = $unformratted_values;

//            $row['_level'] = 0;
        }

        return $row;
    }

    protected function processRules($value, $rules) {
        //check whether there are more than one rules
        $rules = explode('|', $rules);

        foreach ($rules as $rule) {
            $args = [];

            if (strpos($rule, ':') !== false) {
                $args = explode(':', $rule);
                $rule = array_shift($args);
            }

            $value = call_user_func_array([$this, $rule], array_merge([$value], $args));
        }

        return $value;
    }

    //Define all the rules
    protected function decimal($value, $decimalPoint = 2) {
        return round($value, $decimalPoint);
    }

    protected function percentage($value): string {
        return $value . '%';
    }

    protected function number($value, $decimalPoint = 0): string {
        $value = str_replace(',', '', $value);
        return number_format((string)$value, $decimalPoint);
    }

    protected function jsondecode($value): array {
        return json_decode($value, true);
    }

    protected function currency($value, $currencySymbol = 'gbp') {
        $currencySymbol = strtoupper($currencySymbol);
        $moneyFormatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);

        return $moneyFormatter->formatCurrency($value, $currencySymbol);
    }
}
