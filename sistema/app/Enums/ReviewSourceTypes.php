<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ReviewSourceTypes extends Enum
{
    const Link = "Link";
    const Citation  = "Citation";
}
