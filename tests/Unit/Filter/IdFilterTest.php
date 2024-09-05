<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Tests\Unit\Filter;

use NickSmit\OpenF1Api\Exception\InvalidArgumentException;
use NickSmit\OpenF1Api\Filter\FilterOperator;
use NickSmit\OpenF1Api\Filter\IdFilter;
use PHPUnit\Framework\TestCase;

final class IdFilterTest extends TestCase
{
    public function test_exact_id_filter(): void
    {
        $filter = IdFilter::id(1);

        $this->assertFalse($filter->latest);
        $this->assertSame(1, $filter->id);
        $this->assertSame(1, $filter->getValue());
        $this->assertEquals(FilterOperator::Equal, $filter->getFilterOperator());
    }

    public function test_latest_id_filter(): void
    {
        $filter = IdFilter::latest();

        $this->assertTrue($filter->latest);
        $this->assertNull($filter->id);
        $this->assertSame('latest', $filter->getValue());
        $this->assertEquals(FilterOperator::Equal, $filter->getFilterOperator());
    }

    public function test_an_id_filter_cannot_be_created_with_id_and_latest(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new IdFilter(1, true);
    }

    public function test_an_id_filter_cannot_be_created_without_id_and_latest_false(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new IdFilter(null, false);
    }
}
