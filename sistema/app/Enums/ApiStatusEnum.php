<?php

namespace App\Enums;

use App\Models\Api;
use App\Traits\User\EnumBaseTrait;

enum ApiStatusEnum: string
{
    use EnumBaseTrait;


    case Active = 'Active';

    case Inactive = 'Inactive';


    public static function getDefault()
    {
        return self::Active->value;
    }

    public static function getModel(): Api
    {
        return new Api();
    }

    public static function getColName()
    {
        return 'status';
    }

}
