<?php

namespace App\Exceptions;

use App\Traits\ExceptionTrait;
use Exception;

class RepositoryException extends Exception
{
    use ExceptionTrait;
    /**
     * @return RepositoryException
     */
    public static function recordNotCreated(): self
    {
        return new self(trans('Record not created.'));
    }
    /**
     * @return RepositoryException
     */
    public static function recordNotDeleted(): self
    {
        return new self(trans('Record not created.'));
    }


    public static function balanceError(): self
    {
        return new self(__("Balance not debited/credited."));
    }
}
