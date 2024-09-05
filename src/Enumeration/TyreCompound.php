<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum TyreCompound: string
{
    case Soft = 'SOFT';
    case Medium = 'MEDIUM';
    case Hard = 'HARD';
    case Intermediate = 'INTERMEDIATE';
    case Wet = 'WET';
    case TestUnknown = 'TEST_UNKNOWN';
    case Unknown = 'UNKNOWN';
}