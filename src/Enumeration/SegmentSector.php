<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum SegmentSector
{
    case Unknown;

    case Yellow;

    case Green;

    case Purple;

    case Pitlane;

    public static function fromInt(int $value): self
    {
        return match ($value) {
            2048 => self::Yellow,
            2049 => self::Green,
            2051 => self::Purple,
            2064 => self::Pitlane,
            default => self::Unknown,
        };
    }
}
