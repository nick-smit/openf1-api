<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Drivers;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\IdFilter;

class DriversRequest extends AbstractRequest
{
    /**
     * @return Driver[]
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?string   $broadcastName,
        ?string   $countryCode,
        ?int      $driverNumber,
        ?string   $firstName,
        ?string   $fullName,
        ?string   $headshotUrl,
        ?string   $lastName,
        ?IdFilter $meetingKey,
        ?string   $nameAcronym,
        ?IdFilter $sessionKey,
        ?string   $teamColour,
        ?string   $teamName,
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

        $response = $this->executeRequest('v1/drivers', $queryParams);

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
        ), $response);

    }
}
