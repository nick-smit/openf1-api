<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint\Intervals;

use NickSmit\OpenF1Api\Exception\InvalidArgumentException;

readonly class TimeGap
{
    /**
     * @var bool Whether the car is the leader of the race
     */
    public bool $isLeader;

    /**
     * @var bool Whether the car is lapped or not
     */
    public bool $isLapped;

    /**
     * @var int|null The amount of laps the car is lapped by, null if the car is not lapped)
     */
    public ?int $lappedCount;

    /**
     * @var float|null The time gap in seconds
     */
    public ?float $gap;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        float|string|null $gap
    ) {
        $this->isLeader = $gap === 0.0 || $gap === null;
        if (is_float($gap) || $gap === null) {
            $this->gap = $gap > 0 ? $gap : null;
            $this->isLapped = false;
            $this->lappedCount = null;
        } elseif (str_ends_with($gap, ' LAP')) {
            $this->gap = null;
            $this->isLapped = true;
            $this->lappedCount = (int)filter_var($gap, FILTER_SANITIZE_NUMBER_INT);
        } else {
            throw new InvalidArgumentException(sprintf('Value for \$gap (%s) is not valid', $gap));
        }
    }
}
