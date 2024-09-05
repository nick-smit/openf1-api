<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum RaceControlScope: string
{
    case Track = 'Track';
    case Driver = 'Driver';
    case Sector = 'Sector';
}