<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Stints;

readonly class Stint
{
    /**
     * @param TyreCompound|null $compound The specific compound of tyre used during the stint (SOFT, MEDIUM, HARD, ...).
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param int|null $lapEnd Number of the last completed lap in this stint.
     * @param int|null $lapStart Number of the initial lap in this stint (starts at 1).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param int|null $stintNumber The sequential number of the stint within the session (starts at 1).
     * @param int|null $tyreAgeAtStart The age of the tyres at the start of the stint, in laps completed.
     */
    public function __construct(
        public ?TyreCompound $compound,
        public ?int          $driverNumber,
        public ?int          $lapEnd,
        public ?int          $lapStart,
        public int           $meetingKey,
        public int           $sessionKey,
        public ?int          $stintNumber,
        public ?int          $tyreAgeAtStart
    ) {
    }
}
