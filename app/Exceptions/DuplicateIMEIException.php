<?php

namespace App\Exceptions;

use Exception;

class DuplicateIMEIException extends Exception
{
    public $message = 'Duplicate IMEI.';
}
