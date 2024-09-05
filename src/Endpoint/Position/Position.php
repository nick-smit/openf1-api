<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Position;

use DateTimeImmutable;

readonly class Position
{
    /**
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $position Position of the driver (starts at 1).
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     */
    public function __construct(
        public DateTimeImmutable $date,
        public int               $driverNumber,
        public int               $meetingKey,
        public int               $position,
        public int               $sessionKey,
    ) {

    }
}
