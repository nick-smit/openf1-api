<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use DateTimeImmutable;
use DateTimeZone;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;
use PHPUnit\Framework\TestCase;

final class DateTimeImmutableFromApiResponseTest extends TestCase
{
    use DateTimeImmutableFromApiResponse;

    public function test_the_api_format_can_be_transformed(): void
    {
        $result = $this->transformApiDate('2020-01-13T08:00:05.123456+00:00');

        $this->assertEquals(new DateTimeImmutable(
            '2020-01-13 08:00:05.123456',
            new DateTimeZone('UTC')
        ), $result);
    }

    public function test_the_other_api_format_can_be_transformed(): void
    {
        // Yay, inconsistency.
        $result = $this->transformApiDate('2020-01-13T08:00:05+00:00');

        $this->assertEquals(new DateTimeImmutable(
            '2020-01-13 08:00:05',
            new DateTimeZone('UTC')
        ), $result);
    }

    public function test_an_invalid_format_throws_an_exception(): void
    {
        $this->expectException(UnexpectedResponseException::class);

        $this->transformApiDate('2020-01-13 00:00');
    }
}
