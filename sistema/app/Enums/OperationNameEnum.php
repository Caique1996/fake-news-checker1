<?php

namespace App\Enums;

namespace App\Enums;

use App\Traits\User\EnumBaseTrait;

enum OperationNameEnum: string
{
    use EnumBaseTrait;

    case List = 'list';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case Show = 'show';
    case Reorder = 'reorder';
    case Changeorderstatus = 'Changeorderstatus';
}
