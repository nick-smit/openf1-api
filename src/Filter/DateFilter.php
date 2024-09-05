<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Filter;

use DateTimeInterface;
use NickSmit\OpenF1Api\Exception\InvalidArgumentException;

readonly class DateFilter
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        public ?DateTimeInterface $exactDate = null,
        public ?DateTimeInterface $afterDate = null,
        public ?DateTimeInterface $beforeDate = null
    ) {
        if (!$this->exactDate instanceof DateTimeInterface && !$this->afterDate instanceof DateTimeInterface && !$this->beforeDate instanceof DateTimeInterface) {
            throw new InvalidArgumentException('Either one of exactDate, afterDate or beforeDate must not be null.');
        }

        if ($this->exactDate instanceof \DateTimeInterface && ($this->afterDate instanceof DateTimeInterface || $this->beforeDate instanceof DateTimeInterface)) {
            throw new InvalidArgumentException('exactDate cannot be used in combination with afterDate and beforeDate parameters.');
        }
    }

    public static function exactDate(DateTimeInterface $exactDate): self
    {
        return new self(exactDate: $exactDate);
    }

    public static function between(DateTimeInterface $afterDate, DateTimeInterface $beforeDate): self
    {
        return new self(afterDate: $afterDate, beforeDate: $beforeDate);
    }

    public static function afterDate(DateTimeInterface $afterDate): self
    {
        return new self(afterDate: $afterDate);
    }

    public static function beforeDate(DateTimeInterface $beforeDate): self
    {
        return new self(beforeDate: $beforeDate);
    }
}
