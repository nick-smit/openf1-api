<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Location;

use DateTimeImmutable;

class Location
{
    /**
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param int $x The 'x' value in a 3D Cartesian coordinate system representing the current approximate location of the car on the track.
     * @param int $y The 'y' value in a 3D Cartesian coordinate system representing the current approximate location of the car on the track.
     * @param int $z The 'z' value in a 3D Cartesian coordinate system representing the current approximate location of the car on the track.
     */
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly int               $driverNumber,
        public readonly int               $meetingKey,
        public readonly int               $sessionKey,
        public readonly int               $x,
        public readonly int               $y,
        public readonly int               $z,
    ) {
    }
}
