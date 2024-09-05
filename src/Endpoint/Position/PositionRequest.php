<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Position;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class PositionRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return Position[]
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?DateFilter   $date,
        ?int          $driverNumber,
        ?IdFilter     $meetingKey,
        ?NumberFilter $position,
        ?IdFilter     $sessionKey,
    ): array {
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'meeting_key' => $meetingKey,
            'position' => $position,
            'session_key' => $sessionKey,
        ]);

        $response = $this->executeRequest('v1/position', $queryParams);

        return array_map(fn (array $item): Position => new Position(
            $this->transformApiDate($item['date']),
            $item['driver_number'],
            $item['meeting_key'],
            $item['position'],
            $item['session_key'],
        ), $response);
    }
}
