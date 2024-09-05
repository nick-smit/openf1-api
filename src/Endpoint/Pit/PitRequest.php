<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Pit;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class PitRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return Pit[]
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?DateFilter   $date,
        ?int          $driverNumber,
        ?int          $lapNumber,
        ?IdFilter     $meetingKey,
        ?NumberFilter $pitDuration,
        ?IdFilter     $sessionKey,
    ): array {
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'lap_number' => $lapNumber,
            'meeting_key' => $meetingKey,
            'pit_duration' => $pitDuration,
            'session_key' => $sessionKey,
        ]);

        $response = $this->executeRequest('v1/pit', $queryParams);

        return array_map(fn (array $item): Pit => new Pit(
            $this->transformApiDate($item['date']),
            $item['driver_number'],
            $item['lap_number'],
            $item['meeting_key'],
            $item['pit_duration'],
            $item['session_key'],
        ), $response);
    }
}
