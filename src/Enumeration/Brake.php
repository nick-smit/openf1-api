<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum Brake: int
{
    case Engaged = 100;
    case Disengaged = 0;
}
