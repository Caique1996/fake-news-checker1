<?php

namespace App\Exceptions;

use App\Traits\ExceptionTrait;
use Exception;

class ApiException extends Exception
{
    use ExceptionTrait;
}
