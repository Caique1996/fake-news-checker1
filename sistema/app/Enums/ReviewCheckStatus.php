<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;


final class ReviewCheckStatus extends Enum
{
    const Real = 'Real';
    const Fake = 'Fake';
    const RealBut = 'True, but...';
    const ItsIsTooEarly = "It is too early to check";
    const Exaggerated = "Exaggerated";
    const Underrated = "Underrated";
    const Contradictory = "Contradictory";
    const Unsustainable = "Unsustainable";
    const UnderMonitoring = "Under monitoring";

}
