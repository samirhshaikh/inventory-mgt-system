<?php

namespace App\Exceptions;

use Exception;

class InvalidDataException extends Exception
{
    public $message = 'Invalid Data.';
}
