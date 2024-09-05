<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Sessions;

use DateTimeImmutable;
use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class SessionsRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     *
     * @return Session[]
     *
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?int               $circuitKey,
        ?string            $circuitShortName,
        ?string            $countryCode,
        ?int               $countryKey,
        ?string            $countryName,
        ?DateTimeImmutable $dateEnd,
        ?DateTimeImmutable $dateStart,
        ?string            $gmtOffset,
        ?string            $location,
        ?IdFilter          $meetingKey,
        ?IdFilter          $sessionKey,
        ?string            $sessionName,
        ?SessionType       $sessionType,
        ?int               $year,
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

        $response = $this->executeRequest('v1/sessions', $queryParams);

        return array_map(fn (array $item): Session => new Session(
            $item['circuit_key'],
            $item['circuit_short_name'],
            $item['country_code'],
            $item['country_key'],
            $item['country_name'],
            $this->transformApiDate($item['date_end']),
            $this->transformApiDate($item['date_start']),
            $item['gmt_offset'],
            $item['location'],
            $item['meeting_key'],
            $item['session_key'],
            $item['session_name'],
            SessionType::from($item['session_type']),
            $item['year'],
        ), $response);

    }
}
