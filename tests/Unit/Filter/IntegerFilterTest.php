<?php
declare(strict_types=1);

namespace Tests\Unit\Filter;

use NickSmit\OpenF1Api\Filter\FilterOperator;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use PHPUnit\Framework\TestCase;

class IntegerFilterTest extends TestCase
{
    public function test_integer_filter_can_be_created(): void
    {
        $filter = new NumberFilter(100, FilterOperator::Equal);

        self::assertEquals(100, $filter->value);
        self::assertEquals(100, $filter->getValue());
        self::assertEquals(FilterOperator::Equal, $filter->filterOperator);
        self::assertEquals(FilterOperator::Equal, $filter->getFilterOperator());
    }

    public function test_integer_filter_can_be_created_without_a_filter_operator(): void
    {
        $filter = new NumberFilter(100);

        self::assertEquals(100, $filter->value);
        self::assertEquals(FilterOperator::Equal, $filter->filterOperator);
    }

    public function test_integer_filter_can_be_created_through_the_equal_named_constructor(): void
    {
        $filter = NumberFilter::equal(100);

        self::assertEquals(100, $filter->value);
        self::assertEquals(FilterOperator::Equal, $filter->filterOperator);
    }

    public function test_integer_filter_can_be_created_through_the_greater_than_named_constructor(): void
    {
        $filter = NumberFilter::greaterThan(0);

        self::assertEquals(0, $filter->value);
        self::assertEquals(FilterOperator::GreaterThan, $filter->filterOperator);
    }

    public function test_integer_filter_can_be_created_through_the_less_than_named_constructor(): void
    {
        $filter = NumberFilter::lessThan(100);

        self::assertEquals(100, $filter->value);
        self::assertEquals(FilterOperator::LessThan, $filter->filterOperator);
    }
}