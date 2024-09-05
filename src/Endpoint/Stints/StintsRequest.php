<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Stints;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;

class StintsRequest extends AbstractRequest
{
    /**
     *
     * @return Stint[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?TyreCompound $compound,
        ?int          $driverNumber,
        ?NumberFilter $lapEnd,
        ?NumberFilter $lapStart,
        ?IdFilter     $meetingKey,
        ?IdFilter     $sessionKey,
        ?NumberFilter $stintNumber,
        ?NumberFilter $tyreAgeAtStart,
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

        $response = $this->executeRequest('v1/stints', $queryParams);

        return array_map(static fn (array $item): Stint => new Stint(
            $item['compound'] !== null ? TyreCompound::from($item['compound']) : null,
            $item['driver_number'],
            $item['lap_end'],
            $item['lap_start'],
            $item['meeting_key'],
            $item['session_key'],
            $item['stint_number'],
            $item['tyre_age_at_start'],
        ), $response);
    }
}
