<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\Ranges;

readonly class TimestampTz extends DateTime
{
    protected function format(): string
    {
        return 'Y-m-d H:i:sP';
    }

    protected function castAs(): string
    {
        return 'tstzrange';
    }
}
