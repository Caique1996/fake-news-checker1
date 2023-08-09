<?php

namespace App\Exceptions;

use Exception;

class SoftException extends Exception
{
    public static function orderAlreadyConfigured(): self
    {
        return new self(__("This product has already been configured. Complete validation and/or reissue."));
    }
    public static function reissueNotAvailable(): self
    {
        return new self(__("Reissue is not available for this certificate."));
    }
}
