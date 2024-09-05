<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Tests\Unit\Factory;

use DateTimeImmutable;
use DateTimeZone;
use NickSmit\OpenF1Api\Endpoint\RaceControl\Flag;
use NickSmit\OpenF1Api\Factory\QueryParameterFactory;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use PHPUnit\Framework\TestCase;

final class QueryParameterFactoryTest extends TestCase
{
    private QueryParameterFactory $queryParameterFactory;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->queryParameterFactory = new QueryParameterFactory();
    }

    public function test_null_params_are_filtered(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'some_var' => '1',
            'other_var' => null,
        ]);

        $this->assertSame(['some_var' => '1'], $result);
    }

    public function test_multiple_vars_are_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'some_var' => '1',
            'other_var' => '2',
        ]);

        $this->assertSame(['some_var' => '1', 'other_var' => '2'], $result);
    }

    public function test_an_input_filter_greater_than_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'input_filter_var' => NumberFilter::greaterThan(1),
        ]);

        $this->assertSame(['input_filter_var>' => 1], $result);
    }

    public function test_an_input_filter_less_than_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'input_filter_var' => NumberFilter::lessThan(1),
        ]);

        $this->assertSame(['input_filter_var<' => 1], $result);
    }

    public function test_an_input_filter_equal_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'input_filter_var' => NumberFilter::equal(1),
        ]);

        $this->assertSame(['input_filter_var' => 1], $result);
    }

    public function test_a_date_filter_exact_date_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'date' => DateFilter::exactDate(new DateTimeImmutable('2020-01-01', new DateTimeZone('UTC'))),
        ]);

        $this->assertSame(['date' => '2020-01-01T00:00:00+00:00'], $result);
    }

    public function test_a_date_filter_before_date_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'date' => DateFilter::beforeDate(new DateTimeImmutable('2020-01-01', new DateTimeZone('UTC'))),
        ]);

        $this->assertSame(['date_end<' => '2020-01-01T00:00:00+00:00'], $result);
    }

    public function test_a_date_filter_after_date_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'date' => DateFilter::afterDate(new DateTimeImmutable('2020-01-01', new DateTimeZone('UTC'))),
        ]);

        $this->assertSame(['date_start>' => '2020-01-01T00:00:00+00:00'], $result);
    }

    public function test_a_date_filter_between_date_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'date' => DateFilter::between(new DateTimeImmutable('2020-01-01', new DateTimeZone('UTC')), new DateTimeImmutable('2021-01-01', new DateTimeZone('UTC'))),
        ]);

        $this->assertSame(['date_start>' => '2020-01-01T00:00:00+00:00', 'date_end<' => '2021-01-01T00:00:00+00:00'], $result);
    }

    public function test_a_backed_enum_is_handled(): void
    {
        $result = $this->queryParameterFactory->createParameters([
            'some_var' => Flag::Yellow,
        ]);

        $this->assertSame(['some_var' => 'YELLOW'], $result);
    }
}
