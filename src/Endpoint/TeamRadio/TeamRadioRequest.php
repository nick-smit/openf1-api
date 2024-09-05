<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\TeamRadio;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\DateFilter;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class TeamRadioRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return TeamRadio[]
     * @throws ApiUnavailableException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?DateFilter $date,
        ?int        $driverNumber,
        ?IdFilter   $meetingKey,
        ?IdFilter   $sessionKey,
    ): array {
        $queryParams = $this->queryParameterFactory->createParameters([
            'date' => $date,
            'driver_number' => $driverNumber,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
        ]);

        $response = $this->executeRequest('v1/team_radio', $queryParams);

        return array_map(fn (array $item): TeamRadio => new TeamRadio(
            $this->transformApiDate($item['date']),
            $item['driver_number'],
            $item['meeting_key'],
            $item['recording_url'],
            $item['session_key'],
        ), $response);
    }
}
