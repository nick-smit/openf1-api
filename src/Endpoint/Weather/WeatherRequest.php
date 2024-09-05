<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Weather;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class WeatherRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return Weather[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?NumberFilter $airTemperature,
        ?DateFilter   $date,
        ?NumberFilter $humidity,
        ?IdFilter     $meetingKey,
        ?NumberFilter $pressure,
        ?NumberFilter $rainfall,
        ?IdFilter     $sessionKey,
        ?NumberFilter $trackTemperature,
        ?NumberFilter $windDirection,
        ?NumberFilter $windSpeed,
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

        $response = $this->executeRequest('v1/weather', $queryParams);

        return array_map(fn (array $item): Weather => new Weather(
            $item['air_temperature'],
            $this->transformApiDate($item['date']),
            $item['humidity'],
            $item['meeting_key'],
            $item['pressure'],
            $item['rainfall'],
            $item['session_key'],
            $item['track_temperature'],
            $item['wind_direction'],
            $item['wind_speed'],
        ), $response);
    }
}
