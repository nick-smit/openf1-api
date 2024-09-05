<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Client;

use DateTimeImmutable;
use DateTimeInterface;
use NickSmit\OpenF1Api\Endpoint\CarData\CarData;
use NickSmit\OpenF1Api\Endpoint\CarData\CarDataRequest;
use NickSmit\OpenF1Api\Endpoint\Drivers\Driver;
use NickSmit\OpenF1Api\Endpoint\Drivers\DriversRequest;
use NickSmit\OpenF1Api\Endpoint\Intervals\Interval;
use NickSmit\OpenF1Api\Endpoint\Intervals\IntervalsRequest;
use NickSmit\OpenF1Api\Endpoint\Laps\Lap;
use NickSmit\OpenF1Api\Endpoint\Laps\LapsRequest;
use NickSmit\OpenF1Api\Endpoint\Location\Location;
use NickSmit\OpenF1Api\Endpoint\Location\LocationRequest;
use NickSmit\OpenF1Api\Endpoint\Meetings\Meeting;
use NickSmit\OpenF1Api\Endpoint\Meetings\MeetingsRequest;
use NickSmit\OpenF1Api\Endpoint\Pit\Pit;
use NickSmit\OpenF1Api\Endpoint\Pit\PitRequest;
use NickSmit\OpenF1Api\Endpoint\Position\Position;
use NickSmit\OpenF1Api\Endpoint\Position\PositionRequest;
use NickSmit\OpenF1Api\Endpoint\RaceControl\Category;
use NickSmit\OpenF1Api\Endpoint\RaceControl\Flag;
use NickSmit\OpenF1Api\Endpoint\RaceControl\RaceControl;
use NickSmit\OpenF1Api\Endpoint\RaceControl\RaceControlRequest;
use NickSmit\OpenF1Api\Endpoint\RaceControl\Scope;
use NickSmit\OpenF1Api\Endpoint\Sessions\Session;
use NickSmit\OpenF1Api\Endpoint\Sessions\SessionsRequest;
use NickSmit\OpenF1Api\Endpoint\Sessions\SessionType;
use NickSmit\OpenF1Api\Endpoint\Stints\Stint;
use NickSmit\OpenF1Api\Endpoint\Stints\StintsRequest;
use NickSmit\OpenF1Api\Endpoint\Stints\TyreCompound;
use NickSmit\OpenF1Api\Endpoint\TeamRadio\TeamRadio;
use NickSmit\OpenF1Api\Endpoint\TeamRadio\TeamRadioRequest;
use NickSmit\OpenF1Api\Endpoint\Weather\Weather;
use NickSmit\OpenF1Api\Endpoint\Weather\WeatherRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\InvalidArgumentException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Factory\ApiRequestFactory;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;

readonly class OpenF1ApiClient
{
    public function __construct(
        private ApiRequestFactory $apiRequestFactory,
    ) {
    }

    /**
     * Some data about each car, at a sample rate of about 3.7 Hz.
     * @see https://openf1.org/#car-data
     *
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param NumberFilter|null $nGear Current gear selection, ranging from 1 to 8. 0 indicates neutral or no gear engaged.
     * @param NumberFilter|null $rpm Revolutions per minute of the engine.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param NumberFilter|null $speed Velocity of the car in km/h.
     * @param NumberFilter|null $throttle Percentage of maximum engine power being used.
     *
     * @return CarData[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function carData(
        ?DateFilter   $date = null,
        ?int          $driverNumber = null,
        ?IdFilter     $meetingKey = null,
        ?NumberFilter $nGear = null,
        ?NumberFilter $rpm = null,
        ?IdFilter     $sessionKey = null,
        ?NumberFilter $speed = null,
        ?NumberFilter $throttle = null,
    ): array {
        return $this->apiRequestFactory->create(CarDataRequest::class)->exec(
            $date,
            $driverNumber,
            $meetingKey,
            $nGear,
            $rpm,
            $sessionKey,
            $speed,
            $throttle,
        );
    }

    /**
     * Provides information about drivers for each session.
     * @see https://openf1.org/#drivers
     *
     * @param string|null $broadcastName The driver's name, as displayed on TV.
     * @param string|null $countryCode A code that uniquely identifies the country.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param string|null $firstName The driver's first name.
     * @param string|null $fullName The driver's full name.
     * @param string|null $headshotUrl URL of the driver's face photo.
     * @param string|null $lastName The driver's last name.
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param string|null $nameAcronym Three-letter acronym of the driver's name.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param string|null $teamColour The hexadecimal colour value (RRGGBB) of the driver's team.
     * @param string|null $teamName Name of the driver's team.
     *
     * @return Driver[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function drivers(
        ?string   $broadcastName = null,
        ?string   $countryCode = null,
        ?int      $driverNumber = null,
        ?string   $firstName = null,
        ?string   $fullName = null,
        ?string   $headshotUrl = null,
        ?string   $lastName = null,
        ?IdFilter $meetingKey = null,
        ?string   $nameAcronym = null,
        ?IdFilter $sessionKey = null,
        ?string   $teamColour = null,
        ?string   $teamName = null
    ): array {
        return $this->apiRequestFactory->create(DriversRequest::class)->exec(
            $broadcastName,
            $countryCode,
            $driverNumber,
            $firstName,
            $fullName,
            $headshotUrl,
            $lastName,
            $meetingKey,
            $nameAcronym,
            $sessionKey,
            $teamColour,
            $teamName,
        );
    }

    /**
     * Fetches real-time interval data between drivers and their gap to the race leader.
     * Available during races only, with updates approximately every 4 seconds.
     * @see https://openf1.org/#intervals
     *
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param NumberFilter|null $gapToLeader The time gap to the race leader in seconds.
     * @param NumberFilter|null $interval The time gap to the car ahead in seconds.
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     *
     * @return Interval[]
     *
     * @throws ApiUnavailableException
     * @throws InvalidArgumentException
     * @throws UnexpectedResponseException
     */
    public function intervals(
        ?DateFilter   $date = null,
        ?int          $driverNumber = null,
        ?NumberFilter $gapToLeader = null,
        ?NumberFilter $interval = null,
        ?IdFilter     $meetingKey = null,
        ?IdFilter     $sessionKey = null,
    ): array {
        return $this->apiRequestFactory->create(IntervalsRequest::class)->exec(
            $date,
            $driverNumber,
            $gapToLeader,
            $interval,
            $meetingKey,
            $sessionKey,
        );
    }

    /**
     * Provides detailed information about individual laps.
     * @see https://openf1.org/#laps
     *
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param NumberFilter|null $durationSector1 The time taken, in seconds, to complete the first sector of the lap.
     * @param NumberFilter|null $durationSector2 The time taken, in seconds, to complete the second sector of the lap.
     * @param NumberFilter|null $durationSector3 The time taken, in seconds, to complete the third sector of the lap.
     * @param NumberFilter|null $i1Speed The speed of the car, in km/h, at the first intermediate point on the track.
     * @param NumberFilter|null $i2Speed The speed of the car, in km/h, at the second intermediate point on the track.
     * @param bool|null $isPitOutLap A boolean value indicating whether the lap is an "out lap" from the pit (true if it is, false otherwise).
     * @param NumberFilter|null $lapDuration The total time taken, in seconds, to complete the entire lap.
     * @param int|null $lapNumber The sequential number of the lap within the session (starts at 1).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param NumberFilter|null $stSpeed The speed of the car, in km/h, at the speed trap, which is a specific point on the track where the highest speeds are usually recorded.
     *
     * @return Lap[]
     *
     * @throws ApiUnavailableException
     * @throws InvalidArgumentException
     * @throws UnexpectedResponseException
     */
    public function laps(
        ?int          $driverNumber = null,
        ?NumberFilter $durationSector1 = null,
        ?NumberFilter $durationSector2 = null,
        ?NumberFilter $durationSector3 = null,
        ?NumberFilter $i1Speed = null,
        ?NumberFilter $i2Speed = null,
        ?bool         $isPitOutLap = null,
        ?NumberFilter $lapDuration = null,
        ?int          $lapNumber = null,
        ?IdFilter     $meetingKey = null,
        ?IdFilter     $sessionKey = null,
        ?NumberFilter $stSpeed = null,
    ): array {
        return $this->apiRequestFactory->create(LapsRequest::class)->exec(
            $driverNumber,
            $durationSector1,
            $durationSector2,
            $durationSector3,
            $i1Speed,
            $i2Speed,
            $isPitOutLap,
            $lapDuration,
            $lapNumber,
            $meetingKey,
            $sessionKey,
            $stSpeed,
        );
    }

    /**
     * The approximate location of the cars on the circuit, at a sample rate of about 3.7 Hz.
     * Useful for gauging their progress along the track, but lacks details about lateral placement — i.e. whether the
     * car is on the left or right side of the track. The origin point (0, 0, 0) appears to be arbitrary and not tied
     * to any specific location on the track.
     * @see https://openf1.org/#location
     *
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param int|null $x The 'x' value in a 3D Cartesian coordinate system representing the current approximate location of the car on the track.
     * @param int|null $y The 'y' value in a 3D Cartesian coordinate system representing the current approximate location of the car on the track.
     * @param int|null $z The 'z' value in a 3D Cartesian coordinate system representing the current approximate location of the car on the track.
     *
     * @return Location[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function location(
        ?DateFilter $date = null,
        ?int        $driverNumber = null,
        ?IdFilter   $meetingKey = null,
        ?IdFilter   $sessionKey = null,
        ?int        $x = null,
        ?int        $y = null,
        ?int        $z = null,
    ): array {
        return $this->apiRequestFactory->create(LocationRequest::class)->exec(
            $date,
            $driverNumber,
            $meetingKey,
            $sessionKey,
            $x,
            $y,
            $z,
        );
    }

    /**
     * Provides information about meetings.
     * A meeting refers to a Grand Prix or testing weekend and usually includes multiple sessions (practice, qualifying, race, ...).
     * @see https://openf1.org/#meetings
     *
     * @param int|null $circuitKey The unique identifier for the circuit where the event takes place.
     * @param string|null $circuitShortName The short or common name of the circuit where the event takes place.
     * @param string|null $countryCode A code that uniquely identifies the country.
     * @param int|null $countryKey The unique identifier for the country where the event takes place.
     * @param string|null $countryName The full name of the country where the event takes place.
     * @param DateTimeInterface|null $dateStart The UTC starting date and time, in ISO 8601 format.
     * @param string|null $gmtOffset The difference in hours and minutes between local time at the location of the event and Greenwich Mean Time (GMT).
     * @param string|null $location The city or geographical location where the event takes place.
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param string|null $meetingName The name of the meeting.
     * @param string|null $meetingOfficialName The official name of the meeting.
     * @param int|null $year The year the event takes place.
     *
     * @return Meeting[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function meetings(
        ?int               $circuitKey = null,
        ?string            $circuitShortName = null,
        ?string            $countryCode = null,
        ?int               $countryKey = null,
        ?string            $countryName = null,
        ?DateTimeInterface $dateStart = null,
        ?string            $gmtOffset = null,
        ?string            $location = null,
        ?IdFilter          $meetingKey = null,
        ?string            $meetingName = null,
        ?string            $meetingOfficialName = null,
        ?int               $year = null,
    ): array {
        return $this->apiRequestFactory->create(MeetingsRequest::class)->exec(
            $circuitKey,
            $circuitShortName,
            $countryCode,
            $countryKey,
            $countryName,
            $dateStart,
            $gmtOffset,
            $location,
            $meetingKey,
            $meetingName,
            $meetingOfficialName,
            $year,
        );
    }

    /**
     * Provides information about cars going through the pit lane.
     * @see https://openf1.org/#pit
     *
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param int|null $lapNumber The sequential number of the lap within the session (starts at 1).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param NumberFilter|null $pitDuration The time spent in the pit, from entering to leaving the pit lane, in seconds.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     *
     * @return Pit[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function pit(
        ?DateFilter   $date = null,
        ?int          $driverNumber = null,
        ?int          $lapNumber = null,
        ?IdFilter     $meetingKey = null,
        ?NumberFilter $pitDuration = null,
        ?IdFilter     $sessionKey = null,
    ): array {
        return $this->apiRequestFactory->create(PitRequest::class)->exec(
            $date,
            $driverNumber,
            $lapNumber,
            $meetingKey,
            $pitDuration,
            $sessionKey,
        );
    }

    /**
     * Provides driver positions throughout a session, including initial placement and subsequent changes.
     * @see https://openf1.org/#position
     *
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param NumberFilter|null $position Position of the driver (starts at 1).
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     *
     * @return Position[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function position(
        ?DateFilter   $date = null,
        ?int          $driverNumber = null,
        ?IdFilter     $meetingKey = null,
        ?NumberFilter $position = null,
        ?IdFilter     $sessionKey = null,
    ): array {
        return $this->apiRequestFactory->create(PositionRequest::class)->exec(
            $date,
            $driverNumber,
            $meetingKey,
            $position,
            $sessionKey,
        );
    }

    /**
     * Provides information about race control (racing incidents, flags, safety car, ...).
     * @see https://openf1.org/#race-control
     *
     * @param Category|null $category The category of the event (CarEvent, Drs, Flag, SafetyCar, ...).
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param Flag|null $flag Type of flag displayed (GREEN, YELLOW, DOUBLE YELLOW, CHEQUERED, ...).
     * @param NumberFilter|null $lapNumber The sequential number of the lap within the session (starts at 1).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param string|null $message Description of the event or action.
     * @param Scope|null $scope The scope of the event (Track, Driver, Sector, ...).
     * @param NumberFilter|null $sector Segment ("mini-sector") of the track where the event occurred? (starts at 1).
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     *
     * @return RaceControl[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function raceControl(
        ?Category     $category = null,
        ?DateFilter   $date = null,
        ?int          $driverNumber = null,
        ?Flag         $flag = null,
        ?NumberFilter $lapNumber = null,
        ?IdFilter     $meetingKey = null,
        ?string       $message = null,
        ?Scope        $scope = null,
        ?NumberFilter $sector = null,
        ?IdFilter     $sessionKey = null,
    ): array {
        return $this->apiRequestFactory->create(RaceControlRequest::class)->exec(
            $category,
            $date,
            $driverNumber,
            $flag,
            $lapNumber,
            $meetingKey,
            $message,
            $scope,
            $sector,
            $sessionKey,
        );
    }

    /**
     * Provides information about sessions.
     * A session refers to a distinct period of track activity during a Grand Prix or testing weekend (practice, qualifying, sprint, race, ...).
     * @see https://openf1.org/#sessions
     *
     * @param int|null $circuitKey The unique identifier for the circuit where the event takes place.
     * @param string|null $circuitShortName The short or common name of the circuit where the event takes place.
     * @param string|null $countryCode A code that uniquely identifies the country.
     * @param int|null $countryKey The unique identifier for the country where the event takes place.
     * @param string|null $countryName The full name of the country where the event takes place.
     * @param DateTimeImmutable|null $dateEnd The UTC ending date and time, in ISO 8601 format.
     * @param DateTimeImmutable|null $dateStart The UTC starting date and time, in ISO 8601 format.
     * @param string|null $gmtOffset The difference in hours and minutes between local time at the location of the event and Greenwich Mean Time (GMT).
     * @param string|null $location The city or geographical location where the event takes place.
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param string|null $sessionName The name of the session (Practice 1, Qualifying, Race, ...).
     * @param SessionType|null $sessionType The type of the session (Practice, Qualifying, Race, ...).
     * @param int|null $year The year the event takes place.
     *
     * @return Session[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function sessions(
        ?int               $circuitKey = null,
        ?string            $circuitShortName = null,
        ?string            $countryCode = null,
        ?int               $countryKey = null,
        ?string            $countryName = null,
        ?DateTimeImmutable $dateEnd = null,
        ?DateTimeImmutable $dateStart = null,
        ?string            $gmtOffset = null,
        ?string            $location = null,
        ?IdFilter          $meetingKey = null,
        ?IdFilter          $sessionKey = null,
        ?string            $sessionName = null,
        ?SessionType       $sessionType = null,
        ?int               $year = null,
    ): array {
        return $this->apiRequestFactory->create(SessionsRequest::class)->exec(
            $circuitKey,
            $circuitShortName,
            $countryCode,
            $countryKey,
            $countryName,
            $dateEnd,
            $dateStart,
            $gmtOffset,
            $location,
            $meetingKey,
            $sessionKey,
            $sessionName,
            $sessionType,
            $year,
        );
    }

    /**
     * Provides information about individual stints.
     * A stint refers to a period of continuous driving by a driver during a session.
     * @see https://openf1.org/#sessions
     *
     * @param TyreCompound|null $compound The specific compound of tyre used during the stint (SOFT, MEDIUM, HARD, ...).
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param NumberFilter|null $lapEnd Number of the last completed lap in this stint.
     * @param NumberFilter|null $lapStart Number of the initial lap in this stint (starts at 1).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param NumberFilter|null $stintNumber The sequential number of the stint within the session (starts at 1).
     * @param NumberFilter|null $tyreAgeAtStart The age of the tyres at the start of the stint, in laps completed.
     *
     * @return Stint[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function stints(
        ?TyreCompound $compound = null,
        ?int          $driverNumber = null,
        ?NumberFilter $lapEnd = null,
        ?NumberFilter $lapStart = null,
        ?IdFilter     $meetingKey = null,
        ?IdFilter     $sessionKey = null,
        ?NumberFilter $stintNumber = null,
        ?NumberFilter $tyreAgeAtStart = null,
    ): array {
        return $this->apiRequestFactory->create(StintsRequest::class)->exec(
            $compound,
            $driverNumber,
            $lapEnd,
            $lapStart,
            $meetingKey,
            $sessionKey,
            $stintNumber,
            $tyreAgeAtStart,
        );
    }

    /**
     * Provides a collection of radio exchanges between Formula 1 drivers and their respective teams during sessions.
     * Please note that only a limited selection of communications are included, not the complete record of radio interactions.
     * @see https://openf1.org/#team-radio
     *
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     *
     * @return TeamRadio[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function teamRadio(
        ?DateFilter $date = null,
        ?int        $driverNumber = null,
        ?IdFilter   $meetingKey = null,
        ?IdFilter   $sessionKey = null,
    ): array {
        return $this->apiRequestFactory->create(TeamRadioRequest::class)->exec(
            $date,
            $driverNumber,
            $meetingKey,
            $sessionKey,
        );
    }

    /**
     * The weather over the track, updated every minute.
     * @see https://openf1.org/#weather
     *
     * @param NumberFilter|null $airTemperature Air temperature (°C).
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param NumberFilter|null $humidity Relative humidity (%).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param NumberFilter|null $pressure Air pressure (mbar).
     * @param NumberFilter|null $rainfall Whether there is rainfall.
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     * @param NumberFilter|null $trackTemperature Track temperature (°C).
     * @param NumberFilter|null $windDirection Wind direction (°), from 0° to 359°.
     * @param NumberFilter|null $windSpeed Wind speed (m/s).
     *
     * @return Weather[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function weather(
        ?NumberFilter $airTemperature = null,
        ?DateFilter   $date = null,
        ?NumberFilter $humidity = null,
        ?IdFilter     $meetingKey = null,
        ?NumberFilter $pressure = null,
        ?NumberFilter $rainfall = null,
        ?IdFilter     $sessionKey = null,
        ?NumberFilter $trackTemperature = null,
        ?NumberFilter $windDirection = null,
        ?NumberFilter $windSpeed = null,
    ): array {
        return $this->apiRequestFactory->create(WeatherRequest::class)->exec(
            $airTemperature,
            $date,
            $humidity,
            $meetingKey,
            $pressure,
            $rainfall,
            $sessionKey,
            $trackTemperature,
            $windDirection,
            $windSpeed,
        );
    }
}
