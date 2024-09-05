<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum Flag: string
{
    case Green = 'GREEN';
    case Yellow = 'YELLOW';
    case Red = 'RED';
    case Blue = 'BLUE';
    case DoubleYellow = 'DOUBLE YELLOW';
    case Chequered = 'CHEQUERED';
    case Clear = 'CLEAR';
    case BlackAndWhite = 'BLACK AND WHITE';
}
