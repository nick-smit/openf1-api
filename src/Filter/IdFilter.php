<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Filter;

use NickSmit\OpenF1Api\Exception\InvalidArgumentException;
use Override;

class IdFilter implements InputFilter
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly bool $latest = false,
    ) {
        if ($this->id !== null && $this->latest) {
            throw new InvalidArgumentException('Exact and latest cannot be set at the same time');
        }

        if ($this->id === null && $this->latest === false) {
            throw new InvalidArgumentException('Exact and latest cannot be unset at the same time');
        }
    }

    public static function id(int $value): self
    {
        return new self(id: $value);
    }

    public static function latest(): self
    {
        return new self(latest: true);
    }

    #[Override]
    public function getFilterOperator(): FilterOperator
    {
        return FilterOperator::Equal;
    }

    #[Override]
    public function getValue(): string|int
    {
        if ($this->latest) {
            return 'latest';
        }

        return $this->id;
    }
}
