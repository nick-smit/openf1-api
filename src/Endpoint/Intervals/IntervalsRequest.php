<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Intervals;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\InvalidArgumentException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class IntervalsRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return Interval[]
     *
     * @throws ApiUnavailableException
     * @throws InvalidArgumentException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?DateFilter   $date,
        ?int          $driverNumber,
        ?NumberFilter $gapToLeader,
        ?NumberFilter $interval,
        ?IdFilter     $meetingKey,
        ?IdFilter     $sessionKey,
    ): array {
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'gap_to_leader' => $gapToLeader,
            'interval' => $interval,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
        ]);

        $response = $this->executeRequest('v1/intervals', $queryParams);

        return array_map(fn (array $item): Interval => new Interval(
            $this->transformApiDate($item['date']),
            $item['driver_number'],
            new TimeGap($item['gap_to_leader']),
            new TimeGap($item['interval']),
            $item['meeting_key'],
            $item['session_key'],
        ), $response);
    }
}
