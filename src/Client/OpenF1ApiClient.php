<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Client;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use NickSmit\OpenF1Api\Enumeration\Brake;
use NickSmit\OpenF1Api\Enumeration\DRS;
use NickSmit\OpenF1Api\Enumeration\Flag;
use NickSmit\OpenF1Api\Enumeration\RaceControlCategory;
use NickSmit\OpenF1Api\Enumeration\RaceControlScope;
use NickSmit\OpenF1Api\Enumeration\SegmentSector;
use NickSmit\OpenF1Api\Enumeration\SessionType;
use NickSmit\OpenF1Api\Enumeration\TyreCompound;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\InvalidArgumentException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Factory\QueryParameterFactory;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Response\CarData;
use NickSmit\OpenF1Api\Response\Driver;
use NickSmit\OpenF1Api\Response\Interval\Interval;
use NickSmit\OpenF1Api\Response\Interval\TimeGap;
use NickSmit\OpenF1Api\Response\Lap\Lap;
use NickSmit\OpenF1Api\Response\Location\Location;
use NickSmit\OpenF1Api\Response\Meeting\Meeting;
use NickSmit\OpenF1Api\Response\Pit\Pit;
use NickSmit\OpenF1Api\Response\Position\Position;
use NickSmit\OpenF1Api\Response\RaceControl\RaceControl;
use NickSmit\OpenF1Api\Response\Session\Session;
use NickSmit\OpenF1Api\Response\Stint\Stint;
use NickSmit\OpenF1Api\Response\TeamRadio\TeamRadio;
use NickSmit\OpenF1Api\Response\Weather\Weather;
use Psr\Http\Message\ResponseInterface;

class OpenF1ApiClient
{
    public function __construct(
        private readonly Client                $client,
        private readonly QueryParameterFactory $queryParameterFactory
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'meeting_key' => $meetingKey,
            'n_gear' => $nGear,
            'rpm' => $rpm,
            'session_key' => $sessionKey,
            'speed' => $speed,
            'throttle' => $throttle,
        ]);

        $response = $this->getResponse('v1/car_data', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static function (array $item): CarData {
            $brake = Brake::tryFrom($item['brake']);
            if (!$brake instanceof Brake) {
                throw new UnexpectedResponseException(sprintf('Got unknown value (%s) for parameter brake', $item['brake']));
            }

            return new CarData(
                $brake,
                self::fromIso8601($item['date']),
                $item['driver_number'],
                DRS::fromInt($item['drs']),
                $item['meeting_key'],
                $item['n_gear'],
                $item['rpm'],
                $item['session_key'],
                $item['speed'],
                $item['throttle'],
            );
        }, $decodedResponse);
    }

    /**
     * @throws ApiUnavailableException
     */
    private function getResponse(string $uri, array $queryParams): ResponseInterface
    {
        $queryParams = array_filter($queryParams);

        // Custom query creation as PHP always encodes greater than and less than characters in parameter keys
        $query = '';
        foreach ($queryParams as $key => $value) {
            if ($query !== '') {
                $query .= '&';
            }

            $query .= $key . '=' . urlencode((string)$value);
        }

        try {
            return $this->client->get($uri, [
                RequestOptions::QUERY => $query,
            ]);
        } catch (GuzzleException $guzzleException) {
            throw new ApiUnavailableException($guzzleException->getMessage(), previous: $guzzleException);
        }
    }

    /**
     * @throws UnexpectedResponseException
     */
    private function decodeResponse(ResponseInterface $response): mixed
    {
        try {
            return json_decode($response->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new UnexpectedResponseException('Invalid json response', $response, previous: $jsonException);
        }
    }

    private function assertResponseIsArray(mixed $decodedResponse, ResponseInterface $response): void
    {
        if (!is_array($decodedResponse)) {
            throw new UnexpectedResponseException('Array response was expected', $response);
        }
    }

    /**
     * @throws UnexpectedResponseException
     */
    public static function fromIso8601(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $value, new DateTimeZone('UTC'));

        if ($date === false) {
            $date = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value, new DateTimeZone('UTC'));

            if ($date === false) {
                throw new UnexpectedResponseException(sprintf('Date %s is not in ISO8601 format.', $value));
            }
        }

        return $date;
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'broadcast_name' => $broadcastName,
            'country_code' => $countryCode,
            'driver_number' => $driverNumber,
            'first_name' => $firstName,
            'full_name' => $fullName,
            'headshot_url' => $headshotUrl,
            'last_name' => $lastName,
            'meeting_key' => $meetingKey,
            'name_acronym' => $nameAcronym,
            'session_key' => $sessionKey,
            'team_colour' => $teamColour,
            'team_name' => $teamName,
        ]);

        $response = $this->getResponse('v1/drivers', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Driver => new Driver(
            $item['driver_number'],
            $item['broadcast_name'],
            $item['full_name'],
            $item['name_acronym'],
            $item['meeting_key'],
            $item['session_key'],
            $item['country_code'],
            $item['first_name'],
            $item['headshot_url'],
            $item['last_name'],
            $item['team_colour'],
            $item['team_name'],
        ), $decodedResponse);
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'gap_to_leader' => $gapToLeader,
            'interval' => $interval,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
        ]);

        $response = $this->getResponse('v1/intervals', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Interval => new Interval(
            self::fromIso8601($item['date']),
            $item['driver_number'],
            new TimeGap($item['gap_to_leader']),
            new TimeGap($item['interval']),
            $item['meeting_key'],
            $item['session_key'],
        ), $decodedResponse);
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
     * @return Lap[]
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'driver_number' => $driverNumber,
            'duration_sector_1' => $durationSector1,
            'duration_sector_2' => $durationSector2,
            'duration_sector_3' => $durationSector3,
            'i1_speed' => $i1Speed,
            'i2_speed' => $i2Speed,
            'is_pit_out_lap' => $isPitOutLap,
            'lap_duration' => $lapDuration,
            'lap_number' => $lapNumber,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
            'st_speed' => $stSpeed,
        ]);

        $response = $this->getResponse('v1/laps', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static function (array $item): Lap {
            $segmentSectorToEnum = static fn (int $segment): SegmentSector => SegmentSector::fromInt($segment);

            return new Lap(
                self::fromIso8601($item['date_start']),
                $item['driver_number'],
                $item['duration_sector_1'],
                $item['duration_sector_2'],
                $item['duration_sector_3'],
                $item['i1_speed'],
                $item['i2_speed'],
                $item['is_pit_out_lap'],
                $item['lap_duration'],
                $item['lap_number'],
                $item['meeting_key'],
                array_map($segmentSectorToEnum, $item['segments_sector_1']),
                array_map($segmentSectorToEnum, $item['segments_sector_2']),
                array_map($segmentSectorToEnum, $item['segments_sector_3']),
                $item['session_key'],
                $item['st_speed'],
            );
        }, $decodedResponse);
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
            'x' => $x,
            'y' => $y,
            'z' => $z,
        ]);

        $response = $this->getResponse('v1/location', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Location => new Location(
            self::fromIso8601($item['date']),
            $item['driver_number'],
            $item['meeting_key'],
            $item['session_key'],
            $item['x'],
            $item['y'],
            $item['z'],
        ), $decodedResponse);
    }

    //
    // 	The unique identifier for the circuit where the event takes place.
    //	The short or common name of the circuit where the event takes place.
    //	A code that uniquely identifies the country.
    //	The unique identifier for the country where the event takes place.
    //	The full name of the country where the event takes place.
    //	The UTC ending date and time, in ISO 8601 format.
    //	The UTC starting date and time, in ISO 8601 format.
    //	The difference in hours and minutes between local time at the location of the event and Greenwich Mean Time (GMT).
    //	The city or geographical location where the event takes place.
    //	The unique identifier for the meeting. Use latest to identify the latest or current meeting.
    //	The unique identifier for the session. Use latest to identify the latest or current session.
    //	The name of the session (Practice 1, Qualifying, Race, ...).
    //	The type of the session (Practice, Qualifying, Race, ...).
    //	The year the event takes place.
    //
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'circuit_key' => $circuitKey,
            'circuit_short_name' => $circuitShortName,
            'country_code' => $countryCode,
            'country_key' => $countryKey,
            'country_name' => $countryName,
            'date_start' => $dateStart,
            'gmt_offset' => $gmtOffset,
            'location' => $location,
            'meeting_key' => $meetingKey,
            'meeting_name' => $meetingName,
            'meeting_official_name' => $meetingOfficialName,
            'year' => $year,
        ]);

        $response = $this->getResponse('v1/meetings', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Meeting => new Meeting(
            $item['circuit_key'],
            $item['circuit_short_name'],
            $item['country_code'],
            $item['country_key'],
            $item['country_name'],
            self::fromIso8601($item['date_start']),
            $item['gmt_offset'],
            $item['location'],
            $item['meeting_key'],
            $item['meeting_name'],
            $item['meeting_official_name'],
            $item['year'],
        ), $decodedResponse);
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'lap_number' => $lapNumber,
            'meeting_key' => $meetingKey,
            'pit_duration' => $pitDuration,
            'session_key' => $sessionKey,
        ]);

        $response = $this->getResponse('v1/pit', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Pit => new Pit(
            self::fromIso8601($item['date']),
            $item['driver_number'],
            $item['lap_number'],
            $item['meeting_key'],
            $item['pit_duration'],
            $item['session_key'],
        ), $decodedResponse);
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'meeting_key' => $meetingKey,
            'position' => $position,
            'session_key' => $sessionKey,
        ]);

        $response = $this->getResponse('v1/position', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Position => new Position(
            self::fromIso8601($item['date']),
            $item['driver_number'],
            $item['meeting_key'],
            $item['position'],
            $item['session_key'],
        ), $decodedResponse);
    }

    /**
     * Provides information about race control (racing incidents, flags, safety car, ...).
     * @see https://openf1.org/#race-control
     *
     * @param RaceControlCategory|null $category The category of the event (CarEvent, Drs, Flag, SafetyCar, ...).
     * @param DateFilter|null $date The UTC date and time, in ISO 8601 format.
     * @param int|null $driverNumber The unique number assigned to an F1 driver (cf. Wikipedia).
     * @param Flag|null $flag Type of flag displayed (GREEN, YELLOW, DOUBLE YELLOW, CHEQUERED, ...).
     * @param NumberFilter|null $lapNumber The sequential number of the lap within the session (starts at 1).
     * @param IdFilter|null $meetingKey The unique identifier for the meeting. Use latest to identify the latest or current meeting.
     * @param string|null $message Description of the event or action.
     * @param RaceControlScope|null $scope The scope of the event (Track, Driver, Sector, ...).
     * @param NumberFilter|null $sector Segment ("mini-sector") of the track where the event occurred? (starts at 1).
     * @param IdFilter|null $sessionKey The unique identifier for the session. Use latest to identify the latest or current session.
     *
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function raceControl(
        ?RaceControlCategory $category = null,
        ?DateFilter          $date = null,
        ?int                 $driverNumber = null,
        ?Flag                $flag = null,
        ?NumberFilter        $lapNumber = null,
        ?IdFilter            $meetingKey = null,
        ?string              $message = null,
        ?RaceControlScope    $scope = null,
        ?NumberFilter        $sector = null,
        ?IdFilter            $sessionKey = null,
    ): array {
        $queryParams = $this->queryParameterFactory->createParameters([
            'category' => $category,
            'date' => $date,
            'driver_number' => $driverNumber,
            'flag' => $flag,
            'lap_number' => $lapNumber,
            'meeting_key' => $meetingKey,
            'message' => $message,
            'scope' => $scope,
            'sector' => $sector,
            'session_key' => $sessionKey,
        ]);

        $response = $this->getResponse('v1/race_control', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): RaceControl => new RaceControl(
            RaceControlCategory::from($item['category']),
            self::fromIso8601($item['date']),
            $item['driver_number'],
            $item['flag'] !== null ? Flag::from($item['flag']) : null,
            $item['lap_number'],
            $item['meeting_key'],
            $item['message'],
            $item['scope'] !== null ? RaceControlScope::from($item['scope']) : null,
            $item['sector'],
            $item['session_key'],
        ), $decodedResponse);
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
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     * @see
     *
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'circuit_key' => $circuitKey,
            'circuit_short_name' => $circuitShortName,
            'country_code' => $countryCode,
            'country_key' => $countryKey,
            'country_name' => $countryName,
            'date_end' => $dateEnd,
            'date_start' => $dateStart,
            'gmt_offset' => $gmtOffset,
            'location' => $location,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
            'session_name' => $sessionName,
            'session_type' => $sessionType,
            'year' => $year,
        ]);

        $response = $this->getResponse('v1/sessions', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Session => new Session(
            $item['circuit_key'],
            $item['circuit_short_name'],
            $item['country_code'],
            $item['country_key'],
            $item['country_name'],
            self::fromIso8601($item['date_end']),
            self::fromIso8601($item['date_start']),
            $item['gmt_offset'],
            $item['location'],
            $item['meeting_key'],
            $item['session_key'],
            $item['session_name'],
            SessionType::from($item['session_type']),
            $item['year'],
        ), $decodedResponse);
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'compound' => $compound,
            'driver_number' => $driverNumber,
            'lap_end' => $lapEnd,
            'lap_start' => $lapStart,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
            'stint_number' => $stintNumber,
            'tyre_age_at_start' => $tyreAgeAtStart,
        ]);

        $response = $this->getResponse('v1/stints', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Stint => new Stint(
            $item['compound'] !== null ? TyreCompound::from($item['compound']) : null,
            $item['driver_number'],
            $item['lap_end'],
            $item['lap_start'],
            $item['meeting_key'],
            $item['session_key'],
            $item['stint_number'],
            $item['tyre_age_at_start'],
        ), $decodedResponse);
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
        ]);

        $response = $this->getResponse('v1/team_radio', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): TeamRadio => new TeamRadio(
            self::fromIso8601($item['date']),
            $item['driver_number'],
            $item['meeting_key'],
            $item['recording_url'],
            $item['session_key'],
        ), $decodedResponse);
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
        $queryParams = $this->queryParameterFactory->createParameters([
            'air_temperature' => $airTemperature,
            'date' => $date,
            'humidity' => $humidity,
            'meeting_key' => $meetingKey,
            'pressure' => $pressure,
            'rainfall' => $rainfall,
            'session_key' => $sessionKey,
            'track_temperature' => $trackTemperature,
            'wind_direction' => $windDirection,
            'wind_speed' => $windSpeed,
        ]);

        $response = $this->getResponse('v1/weather', $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return array_map(static fn (array $item): Weather => new Weather(
            $item['air_temperature'],
            self::fromIso8601($item['date']),
            $item['humidity'],
            $item['meeting_key'],
            $item['pressure'],
            $item['rainfall'],
            $item['session_key'],
            $item['track_temperature'],
            $item['wind_direction'],
            $item['wind_speed'],
        ), $decodedResponse);
    }
}
