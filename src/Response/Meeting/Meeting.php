<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Meeting;

use DateTimeInterface;

class Meeting
{
    /**
     * @param int $circuitKey The unique identifier for the circuit where the event takes place.
     * @param string $circuitShortName The short or common name of the circuit where the event takes place.
     * @param string $countryCode A code that uniquely identifies the country.
     * @param int $countryKey The unique identifier for the country where the event takes place.
     * @param string $countryName The full name of the country where the event takes place.
     * @param DateTimeInterface $dateStart The UTC starting date and time, in ISO 8601 format.
     * @param string $gmtOffset The difference in hours and minutes between local time at the location of the event and Greenwich Mean Time (GMT).
     * @param string $location The city or geographical location where the event takes place.
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param string $meetingName The name of the meeting.
     * @param string $meetingOfficialName The official name of the meeting.
     */
    public function __construct(
        public readonly int               $circuitKey,
        public readonly string            $circuitShortName,
        public readonly string            $countryCode,
        public readonly int               $countryKey,
        public readonly string            $countryName,
        public readonly DateTimeInterface $dateStart,
        public readonly string            $gmtOffset,
        public readonly string            $location,
        public readonly int               $meetingKey,
        public readonly string            $meetingName,
        public readonly string            $meetingOfficialName,
        public readonly int               $year,
    ) {
    }
}
