<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\CarData;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class CarDataRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return CarData[]
     *
     * @throws UnexpectedResponseException
     * @throws ApiUnavailableException
     */
    public function exec(
        ?DateFilter   $date,
        ?int          $driverNumber,
        ?IdFilter     $meetingKey,
        ?NumberFilter $nGear,
        ?NumberFilter $rpm,
        ?IdFilter     $sessionKey,
        ?NumberFilter $speed,
        ?NumberFilter $throttle,
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

        $response = $this->executeRequest('v1/car_data', $queryParams);

        return array_map(fn (array $item): CarData => new CarData(
            Brake::from($item['brake']),
            $this->transformApiDate($item['date']),
            $item['driver_number'],
            DRS::fromInt($item['drs']),
            $item['meeting_key'],
            $item['n_gear'],
            $item['rpm'],
            $item['session_key'],
            $item['speed'],
            $item['throttle'],
        ), $response);
    }
}
