<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Filter;

interface InputFilter
{
    public function getFilterOperator(): FilterOperator;

    public function getValue(): mixed;
}