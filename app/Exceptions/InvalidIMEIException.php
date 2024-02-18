<?php

namespace App\Exceptions;

use Exception;

class InvalidIMEIException extends Exception
{
    public $message = "Invalid IMEI.";
}
