<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Meetings;

use DateTimeInterface;
use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class MeetingsRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return Meeting[]
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?int               $circuitKey,
        ?string            $circuitShortName,
        ?string            $countryCode,
        ?int               $countryKey,
        ?string            $countryName,
        ?DateTimeInterface $dateStart,
        ?string            $gmtOffset,
        ?string            $location,
        ?IdFilter          $meetingKey,
        ?string            $meetingName,
        ?string            $meetingOfficialName,
        ?int               $year,
    ): array {
        $queryParams = $this->queryParameterFactory->createParameters([
            'circuit_key' => $circuitKey,
            'circuit_short_name' => $circuitShortName,
            'country_code' => $countryCode,
            'country_key' => $countryKey,
            'country_name' => $countryName,
            'date_start' => $dateStart,
            'gmt_offset' => $gmtOffset,
            'location' => $location,
            'meeting_key' => $meetingKey,
            'meeting_name' => $meetingName,
            'meeting_official_name' => $meetingOfficialName,
            'year' => $year,
        ]);

        $response = $this->executeRequest('v1/meetings', $queryParams);

        return array_map(fn (array $item): Meeting => new Meeting(
            $item['circuit_key'],
            $item['circuit_short_name'],
            $item['country_code'],
            $item['country_key'],
            $item['country_name'],
            $this->transformApiDate($item['date_start']),
            $item['gmt_offset'],
            $item['location'],
            $item['meeting_key'],
            $item['meeting_name'],
            $item['meeting_official_name'],
            $item['year'],
        ), $response);
    }
}
