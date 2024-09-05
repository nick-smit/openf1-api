<?php
declare(strict_types=1);

namespace Tests\Unit\Response;

use DateTime;
use DateTimeImmutable;
use NickSmit\OpenF1Api\Enumeration\Brake;
use NickSmit\OpenF1Api\Enumeration\DRS;
use NickSmit\OpenF1Api\Response\CarData;
use PHPUnit\Framework\TestCase;

class CarDataTest extends TestCase
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

        self::assertEquals(Brake::Disengaged, $carData->brake);
        self::assertEquals(new DateTime('01-01-2024 00:00 UTC'), $carData->date);
        self::assertEquals(5, $carData->driverNumber);
        self::assertEquals(DRS::Off, $carData->DRS);
        self::assertEquals(123, $carData->meetingKey);
        self::assertEquals(5, $carData->nGear);
        self::assertEquals(11000, $carData->rpm);
        self::assertEquals(456, $carData->sessionKey);
        self::assertEquals(300, $carData->speed);
        self::assertEquals(100, $carData->throttle);
    }
}
