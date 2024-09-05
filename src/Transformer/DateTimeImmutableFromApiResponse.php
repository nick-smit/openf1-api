<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Transformer;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;

trait DateTimeImmutableFromApiResponse
{
    /**
     * @throws UnexpectedResponseException
     */
    private function transformApiDate(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $value, new DateTimeZone('UTC'));

        if ($date === false) {
            // Yay, inconsistency!
            $date = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value, new DateTimeZone('UTC'));

            if ($date === false) {
                throw new UnexpectedResponseException(sprintf('Date %s is not in ISO8601 format.', $value));
            }
        }

        return $date;
    }

}
