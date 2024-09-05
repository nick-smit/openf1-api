<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum SessionType: string
{
    case Practice = 'Practice';
    case Qualifying = 'Qualifying';
    case Race = 'Race';
}
