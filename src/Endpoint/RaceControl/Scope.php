<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\RaceControl;

enum Scope: string
{
    case Track = 'Track';
    case Driver = 'Driver';
    case Sector = 'Sector';
}
