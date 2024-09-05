<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\RaceControl;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class RaceControlRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return RaceControl[]
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?Category $category,
        ?DateFilter       $date,
        ?int              $driverNumber,
        ?Flag             $flag,
        ?NumberFilter     $lapNumber,
        ?IdFilter         $meetingKey,
        ?string           $message,
        ?Scope            $scope,
        ?NumberFilter     $sector,
        ?IdFilter         $sessionKey,
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

        $response = $this->executeRequest('v1/race_control', $queryParams);

        return array_map(fn (array $item): RaceControl => new RaceControl(
            Category::from($item['category']),
            $this->transformApiDate($item['date']),
            $item['driver_number'],
            $item['flag'] !== null ? Flag::from($item['flag']) : null,
            $item['lap_number'],
            $item['meeting_key'],
            $item['message'],
            $item['scope'] !== null ? Scope::from($item['scope']) : null,
            $item['sector'],
            $item['session_key'],
        ), $response);
    }
}
