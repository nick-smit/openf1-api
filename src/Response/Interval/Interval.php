<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Interval;

use DateTimeImmutable;

class Interval
{
    /**
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param TimeGap $gapToLeader The time gap to the race leader
     * @param TimeGap $interval The time gap to the car ahead
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     */
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly int               $driverNumber,
        public readonly TimeGap           $gapToLeader,
        public readonly TimeGap           $interval,
        public readonly int               $meetingKey,
        public readonly int               $sessionKey,
    ) {

    }
}
