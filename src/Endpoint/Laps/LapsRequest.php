<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Laps;

use NickSmit\OpenF1Api\Endpoint\AbstractRequest;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\InvalidArgumentException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;
use NickSmit\OpenF1Api\Transformer\DateTimeImmutableFromApiResponse;

class LapsRequest extends AbstractRequest
{
    use DateTimeImmutableFromApiResponse;

    /**
     * @return Lap[]
     *
     * @throws ApiUnavailableException
     * @throws InvalidArgumentException
     * @throws UnexpectedResponseException
     */
    public function exec(
        ?int          $driverNumber,
        ?NumberFilter $durationSector1,
        ?NumberFilter $durationSector2,
        ?NumberFilter $durationSector3,
        ?NumberFilter $i1Speed,
        ?NumberFilter $i2Speed,
        ?bool         $isPitOutLap,
        ?NumberFilter $lapDuration,
        ?int          $lapNumber,
        ?IdFilter     $meetingKey,
        ?IdFilter     $sessionKey,
        ?NumberFilter $stSpeed,
    ): array {
        $queryParams = $this->queryParameterFactory->createParameters([
            'driver_number' => $driverNumber,
            'duration_sector_1' => $durationSector1,
            'duration_sector_2' => $durationSector2,
            'duration_sector_3' => $durationSector3,
            'i1_speed' => $i1Speed,
            'i2_speed' => $i2Speed,
            'is_pit_out_lap' => $isPitOutLap,
            'lap_duration' => $lapDuration,
            'lap_number' => $lapNumber,
            'meeting_key' => $meetingKey,
            'session_key' => $sessionKey,
            'st_speed' => $stSpeed,
        ]);

        $response = $this->executeRequest('v1/laps', $queryParams);

        return array_map(function (array $item): Lap {
            $segmentSectorToEnum = static fn (int $segment): SegmentSector => SegmentSector::fromInt($segment);

            return new Lap(
                $this->transformApiDate($item['date_start']),
                $item['driver_number'],
                $item['duration_sector_1'],
                $item['duration_sector_2'],
                $item['duration_sector_3'],
                $item['i1_speed'],
                $item['i2_speed'],
                $item['is_pit_out_lap'],
                $item['lap_duration'],
                $item['lap_number'],
                $item['meeting_key'],
                array_map($segmentSectorToEnum, $item['segments_sector_1']),
                array_map($segmentSectorToEnum, $item['segments_sector_2']),
                array_map($segmentSectorToEnum, $item['segments_sector_3']),
                $item['session_key'],
                $item['st_speed'],
            );
        }, $response);
    }
}
