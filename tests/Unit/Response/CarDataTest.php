<?php
declare(strict_types=1);

namespace Tests\Unit\Response;

use DateTime;
use DateTimeImmutable;
use NickSmit\OpenF1Api\Enumeration\Brake;
use NickSmit\OpenF1Api\Enumeration\DRS;
use NickSmit\OpenF1Api\Response\CarData;
use PHPUnit\Framework\TestCase;

final class CarDataTest extends TestCase
{
    public function test_car_data_can_be_created(): void
    {
        $carData = new CarData(
            Brake::Disengaged,
            new DateTimeImmutable('01-01-2024 00:00 UTC'),
            5,
            DRS::Off,
            123,
            5,
            11000,
            456,
            300,
            100
        );

        $this->assertEquals(Brake::Disengaged, $carData->brake);
        $this->assertEquals(new DateTime('01-01-2024 00:00 UTC'), $carData->date);
        $this->assertSame(5, $carData->driverNumber);
        $this->assertEquals(DRS::Off, $carData->DRS);
        $this->assertSame(123, $carData->meetingKey);
        $this->assertSame(5, $carData->nGear);
        $this->assertSame(11000, $carData->rpm);
        $this->assertSame(456, $carData->sessionKey);
        $this->assertSame(300, $carData->speed);
        $this->assertSame(100, $carData->throttle);
    }
}
