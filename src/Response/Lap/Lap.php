<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Lap;

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

use DateTimeImmutable;
use NickSmit\OpenF1Api\Enumeration\SegmentSector;
use NickSmit\OpenF1Api\Exception\InvalidArgumentException;

class Lap
{
    /**
     * @param DateTimeImmutable $dateStart The UTC starting date and time, in ISO 8601 format.
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param float $durationSector1 The time taken, in seconds, to complete the first sector of the lap.
     * @param float $durationSector2 The time taken, in seconds, to complete the second sector of the lap.
     * @param float $durationSector3 The time taken, in seconds, to complete the third sector of the lap.
     * @param int $i1Speed The speed of the car, in km/h, at the first intermediate point on the track.
     * @param int $i2Speed The speed of the car, in km/h, at the second intermediate point on the track.
     * @param bool $isPitOutLap A boolean value indicating whether the lap is an "out lap" from the pit (true if it is, false otherwise).
     * @param float $lapDuration The total time taken, in seconds, to complete the entire lap.
     * @param int $lapNumber The sequential number of the lap within the session (starts at 1).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param array $segmentsSector1 A list of values representing the "mini-sectors" within the first sector (see mapping table below).
     * @param array $segmentsSector2 A list of values representing the "mini-sectors" within the second sector (see mapping table below).
     * @param array $segmentsSector3 A list of values representing the "mini-sectors" within the third sector (see mapping table below).
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param int $stSpeed The speed of the car, in km/h, at the speed trap, which is a specific point on the track where the highest speeds are usually recorded.
     * @throws InvalidArgumentException
     */
    public function __construct(
        public readonly DateTimeImmutable $dateStart,
        public readonly int               $driverNumber,
        public readonly float             $durationSector1,
        public readonly float             $durationSector2,
        public readonly float             $durationSector3,
        public readonly int               $i1Speed,
        public readonly int               $i2Speed,
        public readonly bool              $isPitOutLap,
        public readonly float             $lapDuration,
        public readonly int               $lapNumber,
        public readonly int               $meetingKey,
        public readonly array             $segmentsSector1,
        public readonly array             $segmentsSector2,
        public readonly array             $segmentsSector3,
        public readonly int               $sessionKey,
        public readonly int               $stSpeed,
    )
    {
        $this->assertSegmentSector($this->segmentsSector1);
        $this->assertSegmentSector($this->segmentsSector2);
        $this->assertSegmentSector($this->segmentsSector3);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertSegmentSector(array $segmentSecotrArray): void
    {
        foreach ($segmentSecotrArray as $segment) {
            if (!$segment instanceof SegmentSector) {
                throw new InvalidArgumentException('Segment must be type of ' . SegmentSector::class);
            }
        }
    }
}