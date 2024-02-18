<?php

namespace App\Services;

class DateService
{
    public static function convertToMySQL($value)
    {
        $originalDate = \DateTime::createFromFormat("d-M-Y", $value);

        return $originalDate->format("Y-m-d");
    }
}
