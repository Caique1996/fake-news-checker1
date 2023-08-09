<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SearchType extends Enum
{
    const News = 'News';
    const Image = 'Image';
    const Text = 'Text';

}
