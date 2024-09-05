<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Intervals;

use DateTimeImmutable;

readonly class Interval
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
        public DateTimeImmutable $date,
        public int               $driverNumber,
        public TimeGap           $gapToLeader,
        public TimeGap           $interval,
        public int               $meetingKey,
        public int               $sessionKey,
    ) {

    }
}
