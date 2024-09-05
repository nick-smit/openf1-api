<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Pit;

use DateTimeImmutable;

class Pit
{
    /**
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param int $lapNumber The sequential number of the lap within the session (starts at 1).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param float $pitDuration The time spent in the pit, from entering to leaving the pit lane, in seconds.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     */
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly int               $driverNumber,
        public readonly int               $lapNumber,
        public readonly int               $meetingKey,
        public readonly float             $pitDuration,
        public readonly int               $sessionKey,
    )
    {

    }
}