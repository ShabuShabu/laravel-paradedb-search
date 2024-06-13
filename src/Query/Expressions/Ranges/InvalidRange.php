<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

use InvalidArgumentException;

class InvalidRange extends InvalidArgumentException
{
    public static function wrongOrder(): self
    {
        return new self('Range values must be in order from lowest to highest');
    }

    public static function unbounded(): self
    {
        return new self('Both upper and lower values cannot not be unbounded at the same time');
    }
}
