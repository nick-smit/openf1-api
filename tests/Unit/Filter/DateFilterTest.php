<?php
declare(strict_types=1);

namespace Tests\Unit\Filter;

use DateTime;
use NickSmit\OpenF1Api\Exception\InvalidArgumentException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use PHPUnit\Framework\TestCase;

class DateFilterTest extends TestCase
{
    public function test_date_filter_can_be_created_with_exact_date(): void
    {
        $filter = new DateFilter(exactDate: new DateTime('01-01-2024 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->exactDate);
        self::assertNull($filter->beforeDate);
        self::assertNull($filter->afterDate);
    }

    public function test_date_filter_can_be_created_with_before_date(): void
    {
        $filter = new DateFilter(beforeDate: new DateTime('01-01-2024 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->beforeDate);
        self::assertNull($filter->exactDate);
        self::assertNull($filter->afterDate);
    }

    public function test_date_filter_can_be_created_with_after_date(): void
    {
        $filter = new DateFilter(afterDate: new DateTime('01-01-2024 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->afterDate);
        self::assertNull($filter->exactDate);
        self::assertNull($filter->beforeDate);
    }

    public function test_date_filter_can_be_created_with_before_date_and_after_date(): void
    {
        $filter = new DateFilter(afterDate: new DateTime('01-01-2024 00:00'), beforeDate: new DateTime('01-01-2025 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->afterDate);
        self::assertEquals(new DateTime('01-01-2025 00:00'), $filter->beforeDate);
        self::assertNull($filter->exactDate);
    }

    public function test_date_filter_cannot_be_created_with_exact_date_and_after_date(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DateFilter(exactDate: new DateTime('01-01-2024 00:00'), afterDate: new DateTime('01-01-2024 00:00'));
    }

    public function test_date_filter_cannot_be_created_with_exact_date_and_before_date(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DateFilter(exactDate: new DateTime('01-01-2024 00:00'), beforeDate: new DateTime('01-01-2024 00:00'));
    }

    public function test_date_filter_cannot_be_created_with_exact_date_and_before_date_and_after_date(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DateFilter(exactDate: new DateTime('01-01-2024 00:00'), afterDate: new DateTime('01-01-2024 00:00'), beforeDate: new DateTime('01-01-2024 00:00'));
    }

    public function test_date_filter_cannot_be_created_without_a_date(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DateFilter(exactDate: null, afterDate: null, beforeDate: null);
    }

    public function test_date_filter_can_be_created_through_the_exact_date_named_constructor(): void
    {
        $filter = DateFilter::exactDate(new DateTime('01-01-2024 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->exactDate);
        self::assertNull($filter->afterDate);
        self::assertNull($filter->beforeDate);
    }

    public function test_date_filter_can_be_created_through_the_between_named_constructor(): void
    {
        $filter = DateFilter::between(new DateTime('01-01-2024 00:00'), new DateTime('01-01-2025 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->afterDate);
        self::assertEquals(new DateTime('01-01-2025 00:00'), $filter->beforeDate);
        self::assertNull($filter->exactDate);
    }

    public function test_date_filter_can_be_created_through_the_after_date_named_constructor(): void
    {
        $filter = DateFilter::afterDate(new DateTime('01-01-2024 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->afterDate);
        self::assertNull($filter->exactDate);
        self::assertNull($filter->beforeDate);
    }

    public function test_date_filter_can_be_created_through_the_before_date_named_constructor(): void
    {
        $filter = DateFilter::beforeDate(new DateTime('01-01-2024 00:00'));

        self::assertEquals(new DateTime('01-01-2024 00:00'), $filter->beforeDate);
        self::assertNull($filter->exactDate);
        self::assertNull($filter->afterDate);
    }
}