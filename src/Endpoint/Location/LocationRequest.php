<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Location;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class LocationRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     *
     * @return Location[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?DateFilter $date,
        ?int        $driverNumber,
        ?IdFilter   $meetingKey,
        ?IdFilter   $sessionKey,
        ?int        $x,
        ?int        $y,
        ?int        $z,
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

        $response = $this->executeRequest('v1/location', $queryParams);

        return array_map(fn (array $item): Location => new Location(
            $this->transformApiDate($item['date']),
            $item['driver_number'],
            $item['meeting_key'],
            $item['session_key'],
            $item['x'],
            $item['y'],
            $item['z'],
        ), $response);
    }
}
