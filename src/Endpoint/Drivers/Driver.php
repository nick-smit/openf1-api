<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Drivers;

readonly class Driver
{
    /**
     * @param int $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param string $broadcastName The driver's name, as displayed on TV.
     * @param string $fullName The driver's full name.
     * @param string $nameAcronym Three-letter acronym of the driver's name.
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param string|null $countryCode A code that uniquely identifies the country.
     * @param string|null $firstName The driver's first name.
     * @param string|null $headshotUrl URL of the driver's face photo.
     * @param string|null $lastName The driver's last name.
     * @param string|null $teamColour The hexadecimal colour value (RRGGBB) of the driver's team.
     * @param string|null $teamName Name of the driver's team.
     */
    public function __construct(
        public int     $driverNumber,
        public string  $broadcastName,
        public string  $fullName,
        public string  $nameAcronym,
        public int     $meetingKey,
        public int     $sessionKey,
        public ?string $countryCode,
        public ?string $firstName,
        public ?string $headshotUrl,
        public ?string $lastName,
        public ?string $teamColour,
        public ?string $teamName,
    ) {

    }
}
