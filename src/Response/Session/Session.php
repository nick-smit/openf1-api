<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Session;

use DateTimeImmutable;
use NickSmit\OpenF1Api\Enumeration\SessionType;

class Session
{
    /**
     * @param int $circuitKey The unique identifier for the circuit where the event takes place.
     * @param string $circuitShortName The short or common name of the circuit where the event takes place.
     * @param string $countryCode A code that uniquely identifies the country.
     * @param int $countryKey The unique identifier for the country where the event takes place.
     * @param string $countryName The full name of the country where the event takes place.
     * @param DateTimeImmutable $dateEnd The UTC ending date and time, in ISO 8601 format.
     * @param DateTimeImmutable $dateStart The UTC starting date and time, in ISO 8601 format.
     * @param string $gmtOffset The difference in hours and minutes between local time at the location of the event and Greenwich Mean Time (GMT).
     * @param string $location The city or geographical location where the event takes place.
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param string $sessionName The name of the session (Practice 1, Qualifying, Race, ...).
     * @param SessionType $sessionType The type of the session (Practice, Qualifying, Race, ...).
     * @param int $year The year the event takes place.
     */
    public function __construct(
        public readonly int               $circuitKey,
        public readonly string            $circuitShortName,
        public readonly string            $countryCode,
        public readonly int               $countryKey,
        public readonly string            $countryName,
        public readonly DateTimeImmutable $dateEnd,
        public readonly DateTimeImmutable $dateStart,
        public readonly string            $gmtOffset,
        public readonly string            $location,
        public readonly int               $meetingKey,
        public readonly int               $sessionKey,
        public readonly string            $sessionName,
        public readonly SessionType       $sessionType,
        public readonly int               $year,
    )
    {

    }
}