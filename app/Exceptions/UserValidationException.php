<?php

namespace App\Exceptions;

use Exception;

class UserValidationException extends Exception {
    public $message = 'User credentials are invalid.';
}