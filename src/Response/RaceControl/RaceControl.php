<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\RaceControl;

use DateTimeImmutable;
use NickSmit\OpenF1Api\Enumeration\Flag;
use NickSmit\OpenF1Api\Enumeration\RaceControlCategory;
use NickSmit\OpenF1Api\Enumeration\RaceControlScope;

class RaceControl
{
    /**
     * @param RaceControlCategory $category The category of the event (CarEvent, Drs, Flag, SafetyCar, ...).
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param Flag|null $flag Type of flag displayed (GREEN, YELLOW, DOUBLE YELLOW, CHEQUERED, ...).
     * @param int|null $lapNumber The sequential number of the lap within the session (starts at 1).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param string $message Description of the event or action.
     * @param RaceControlScope|null $scope The scope of the event (Track, Driver, Sector, ...).
     * @param int|null $sector Segment ("mini-sector") of the track where the event occurred? (starts at 1).
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     */
    public function __construct(
        public readonly RaceControlCategory $category,
        public readonly DateTimeImmutable   $date,
        public readonly ?int                $driverNumber,
        public readonly ?Flag               $flag,
        public readonly ?int                $lapNumber,
        public readonly int                 $meetingKey,
        public readonly string              $message,
        public readonly ?RaceControlScope   $scope,
        public readonly ?int                $sector,
        public readonly int                 $sessionKey,
    ) {

    }
}
