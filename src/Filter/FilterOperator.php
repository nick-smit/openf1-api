<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Filter;

enum FilterOperator: string
{
    case Equal = '=';
    case GreaterThan = '>';
    case LessThan = '<';
}