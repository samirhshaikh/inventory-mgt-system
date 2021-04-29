<?php

namespace App\Exceptions;

use Exception;

class DuplicateNameException extends Exception
{
    public $message = 'Duplicate Name.';
}
