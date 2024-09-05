<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum RaceControlCategory: string
{
    case CarEvent = 'CarEvent';
    case Drs = 'Drs';
    case Flag = 'Flag';
    case SafetyCar = 'SafetyCar';
    case Other = 'Other';
}
