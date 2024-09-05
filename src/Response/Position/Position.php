<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Position;

use DateTimeImmutable;

class Position
{
    /**
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $position Position of the driver (starts at 1).
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     */
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly int               $driverNumber,
        public readonly int               $meetingKey,
        public readonly int               $position,
        public readonly int               $sessionKey,
    ) {

    }
}
