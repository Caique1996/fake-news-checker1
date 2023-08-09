<?php

namespace App\Exceptions;

use App\Traits\ExceptionTrait;
use Exception;

class UnauthorizedUserException extends Exception
{
    use ExceptionTrait;

    /**
     * @return RepositoryException
     */
    public static function exception(): self
    {
        return new self(trans('Unauthorized user to perform this action.'));
    }
}
