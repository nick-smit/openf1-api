<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\CarData;

enum Brake: int
{
    case Engaged = 100;
    case Disengaged = 0;
}
