<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Filter;

use Override;

class NumberFilter implements InputFilter
{
    public function __construct(
        public readonly int|float      $value,
        public readonly FilterOperator $filterOperator = FilterOperator::Equal
    )
    {
    }

    public static function equal(int|float $value): self
    {
        return new self($value);
    }

    public static function greaterThan(int|float $value): self
    {
        return new self($value, FilterOperator::GreaterThan);
    }

    public static function lessThan(int|float $value): self
    {
        return new self($value, FilterOperator::LessThan);
    }

    #[Override]
    public function getFilterOperator(): FilterOperator
    {
        return $this->filterOperator;
    }

    #[Override]
    public function getValue(): int|float
    {
        return $this->value;
    }
}