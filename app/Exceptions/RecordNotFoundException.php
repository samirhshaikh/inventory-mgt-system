<?php

namespace App\Exceptions;

use Exception;

class RecordNotFoundException extends Exception
{
    public $message = 'Record not found.';
}
