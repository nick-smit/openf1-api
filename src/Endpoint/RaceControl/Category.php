<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\RaceControl;

enum Category: string
{
    case CarEvent = 'CarEvent';
    case Drs = 'Drs';
    case Flag = 'Flag';
    case SafetyCar = 'SafetyCar';
    case Other = 'Other';
}
