<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response;

use DateTimeImmutable;
use NickSmit\OpenF1Api\Enumeration\Brake;
use NickSmit\OpenF1Api\Enumeration\DRS;

class CarData
{
    /**
     * @param Brake $brake Whether the brake pedal is pressed (100) or not (0).
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param DRS $DRS The Drag Reduction System (DRS) status (see mapping table below).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $nGear Current gear selection, ranging from 1 to 8. 0 indicates neutral or no gear engaged.
     * @param int $rpm Revolutions per minute of the engine.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param int $speed Velocity of the car in km/h.
     * @param int $throttle Percentage of maximum engine power being used.
     */
    public function __construct(
        public readonly Brake             $brake,
        public readonly DateTimeImmutable $date,
        public readonly int               $driverNumber,
        public readonly DRS               $DRS,
        public readonly int               $meetingKey,
        public readonly int               $nGear,
        public readonly int               $rpm,
        public readonly int               $sessionKey,
        public readonly int               $speed,
        public readonly int               $throttle
    )
    {
    }
}