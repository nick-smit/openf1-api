<?php
declare(strict_types=1);

namespace NickSmit\OpenF1Api\Enumeration;

enum DRS
{
    /** DRS is off and driver is not eligible */
    case Off;
    /** DRS is off, but drive is eligible once in activation zone */
    case Detected;
    /** DRS is activated */
    case On;
    /** DRS status is unknown */
    case Unknown;

    public static function fromInt(int $value): DRS
    {
        return match ($value) {
            0, 1 => self::Off,
            8 => self::Detected,
            10, 12, 14 => self::On,
            default => self::Unknown,
        };
    }
}
