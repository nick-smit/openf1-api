<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Response\Weather;

use DateTimeImmutable;

class Weather
{
    /**
     * @param float $airTemperature Air temperature (°C).
     * @param DateTimeImmutable $date The UTC date and time, in ISO 8601 format.
     * @param float $humidity Relative humidity (%).
     * @param int $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param float $pressure Air pressure (mbar).
     * @param int $rainfall Whether there is rainfall.
     * @param int $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param float $trackTemperature Track temperature (°C).
     * @param int $windDirection Wind direction (°), from 0° to 359°.
     * @param float $windSpeed Wind speed (m/s).
     */
    public function __construct(
        public readonly float             $airTemperature,
        public readonly DateTimeImmutable $date,
        public readonly float             $humidity,
        public readonly int               $meetingKey,
        public readonly float             $pressure,
        public readonly int               $rainfall,
        public readonly int               $sessionKey,
        public readonly float             $trackTemperature,
        public readonly int               $windDirection,
        public readonly float             $windSpeed
    ) {
    }
}
