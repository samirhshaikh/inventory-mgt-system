<?php

namespace App\Exceptions;

use Exception;

class NotEnoughRightsException extends Exception
{
    public $message = 'Not enough rights.';
}
