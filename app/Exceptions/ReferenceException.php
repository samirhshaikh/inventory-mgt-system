<?php

namespace App\Exceptions;

use Exception;

class ReferenceException extends Exception
{
    public $message = 'Record reference found.';
}
