<?php

namespace App\Datatables;

use Illuminate\Support\Arr;
use NumberFormatter;

abstract class BaseDatatable
{
    public $options = [];
    public $columns = [];

    private $cached_cell_type_validation = [];

    public function __construct()
    {
        $this->options();
        $this->columns();
    }

    abstract public function options();

    public function columns()
    {
        return $this->columns;
    }

    /**
     * @param array $row
     * @param string $rowKey
     * @return mixed
     */
    public function rowTransformer(array $row, string $rowKey)
    {
        //transform the keys
        foreach ($this->columns as $colKey => $column) {
            //if we don't have a value then just carry on
            if (Arr::get($row, $column['key'], null) === null) {
                continue;
            }

            $value = Arr::get($row, $column['key']);

            //apply rules if defined
            if (!empty($column['rules'])) {
                Arr::set($row, $column['key'], $this->processRules($value, $column['rules']));
            }

            //do a check if the type is a actual file
            if (!$this->is_valid_cell_type(Arr::get($column, 'type', null))) {
                Arr::set($column, 'type', null);
            }
        }

        return $row;
    }

    /**
     * @param mixed $type
     * @return bool
     */
    protected function is_valid_cell_type($type): bool
    {
        if (is_null($type)) {
            return false;
        }

        //If we have already cached it then no need to do it again
        if (isset($this->cached_cell_type_validation[$type])) {
            return $this->cached_cell_type_validation[$type];
        }

        $this->cached_cell_type_validation[$type] = file_exists(base_path('resources/js/Datatable/Cells/' . $type . '.vue'));

        return $this->cached_cell_type_validation[$type];
    }

    protected function processRules($value, $rules)
    {
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
    protected function decimal($value, $decimalPoint = 2)
    {
        return round($value, $decimalPoint);
    }

    protected function percentage($value): string
    {
        return $value . '%';
    }

    protected function number($value, $decimalPoint = 0): string
    {
        $value = str_replace(',', '', $value);
        return number_format((string)$value, $decimalPoint);
    }

    protected function jsondecode($value): array
    {
        return json_decode($value, true);
    }

    protected function currency($value, $currencySymbol = 'gbp')
    {
        $currencySymbol = strtoupper($currencySymbol);
        $moneyFormatter = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);

        return $moneyFormatter->formatCurrency($value, $currencySymbol);
    }
}
